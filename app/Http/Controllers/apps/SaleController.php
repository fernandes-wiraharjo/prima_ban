<?php

namespace App\Http\Controllers\apps;

use App\Enums\MovementType;
use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Service;
use App\Models\StockHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
  public function index()
  {
    return view('content.transactions.sale');
  }

  public function get(Request $request)
  {
    $query = Sale::query()
      ->leftJoin('customers', 'customers.id', 'sales.id_customer')
      ->leftJoin('users', 'users.id', 'sales.created_by')
      ->selectRaw(
        'sales.*, DATE_FORMAT(sales.date, "%d %b %Y") as formatted_date, customers.name as customer_name, users.username'
      );

    $sortableColumns = [
      0 => '',
      1 => 'customer_name',
      2 => 'date',
      3 => 'invoice_no',
      // 4 => 'subtotal_price',
      // 5 => 'discount',
      4 => 'final_price',
      // 7 => 'bank_account_no',
      5 => 'status',
      6 => 'username',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'date'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('customers.name', 'like', $searchValue)
          ->orWhere('invoice_no', 'like', $searchValue)
          ->orWhereRaw("DATE_FORMAT(sales.date, '%d %b %Y') LIKE ?", [$searchValue])
          // ->orWhere('subtotal_price', 'like', $searchValue)
          // ->orWhere('discount', 'like', $searchValue)
          ->orWhere('final_price', 'like', $searchValue)
          // ->orWhere('sales.bank_account_no', 'like', $searchValue)
          ->orWhere('status', 'like', $searchValue)
          ->orWhere('users.username', 'like', $searchValue);
      });
    }

    // Apply date range filter
    if ($request->has('start_date') && $request->has('end_date')) {
      $startDate = $request->input('start_date');
      $endDate = $request->input('end_date');
      $query->whereBetween('sales.date', [$startDate, $endDate]);
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $data = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $data,
    ];

    return response()->json($responseData);
  }

  public function indexAdd()
  {
    $customers = Customer::where('is_active', true)
      ->select('id', 'name', 'type')
      ->get();

    $products = DB::table('product_details')
      ->selectRaw(
        'product_details.id, CONCAT(p.name, " - ", sizes.code) as name, final_price_user_cash, final_price_user_tempo,
        final_price_toko_cash, final_price_toko_tempo, "product" as type'
      )
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->where('p.is_active', true);

    $services = DB::table('services')
      ->selectRaw(
        'CONCAT("jasa-", id) as id, name, price as final_price_user_cash, null as final_price_user_tempo,
        null as final_price_toko_cash, null as final_price_toko_tempo, "service" as type'
      )
      ->where('is_active', true);

    $unionQuery = $products->union($services);

    $results = DB::table(DB::raw("({$unionQuery->toSql()}) as sub"))
      ->mergeBindings($unionQuery)
      ->orderBy('name')
      ->get();

    return view('content.transactions.sale-add', ['customers' => $customers, 'products' => $results]);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'id_customer' => 'required|exists:customers,id',
          'discount' => 'required|numeric',
          'bank_account_no' => 'required',
          'status' => 'required|in:belum lunas,lunas',
          'payment_type' => 'required|in:cash,tempo',
          'invoice_no' => 'required|unique:sales,invoice_no',
          'group-a' => 'required|array',
          'group-a.*.item' => 'required',
          'group-a.*.quantity' => 'required',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          // 'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          // 'group-a.*.quantity.min' => 'Quantity must be at least 1.',
          'invoice_no.unique' => 'The invoice number has already been taken.',
        ]
      );
      DB::beginTransaction();

      //subtotal price sale header
      $subtotal_price = 0;

      // Create a new tanda terima instance
      $sale = new Sale();
      $sale->date = $validatedData['date'];
      $sale->id_customer = $validatedData['id_customer'];
      $sale->invoice_no = $validatedData['invoice_no'];
      $sale->subtotal_price = $subtotal_price;
      $sale->discount = $validatedData['discount'];
      $sale->final_price = 0;
      $sale->bank_account_no = $validatedData['bank_account_no'];
      $sale->technician = $request->technician ?? '';
      $sale->note = $request->note ?? '';
      $sale->status = $validatedData['status'];
      $sale->payment_type = $validatedData['payment_type'];
      $sale->created_by = Auth::id();

      $sale->save();

      // Process sale details
      $customer = Customer::find($validatedData['id_customer']);
      foreach ($request->input('group-a') as $item) {
        if (strpos($item['item'], 'jasa-') === 0) {
          $serviceId = substr($item['item'], 5);
          $productDetailId = null;
          $findService = Service::find($serviceId);
          $productPrice = $findService->price;
        } else {
          $findProductDetail = ProductDetail::find($item['item']);
          $serviceId = null;
          $productDetailId = $item['item'];
          $productPrice = 0;

          // Determine the price based on customer type and payment type
          if ($validatedData['payment_type'] === 'cash') {
            if ($customer->type === 'user') {
              $productPrice = $findProductDetail->final_price_user_cash;
            } else {
              $productPrice = $findProductDetail->final_price_toko_cash;
            }
          } else {
            if ($customer->type === 'toko') {
              $productPrice = $findProductDetail->final_price_toko_tempo;
            } else {
              $productPrice = $findProductDetail->final_price_user_tempo;
            }
          }
        }

        // Convert the normalized quantity to a float value
        $item['quantity'] = strtr($item['quantity'], ['.' => '', ',' => '.']);
        $item['quantity'] = floatval($item['quantity']);

        //total price per detail
        $total_price = $item['quantity'] * $productPrice;

        $saleDetail = new SaleDetail();
        $saleDetail->id_sale = $sale->id;
        $saleDetail->id_product_detail = $productDetailId;
        $saleDetail->id_service = $serviceId;
        $saleDetail->quantity = $item['quantity'];
        $saleDetail->price = $productPrice;
        $saleDetail->total_price = $total_price;
        $saleDetail->created_by = Auth::id();
        $saleDetail->save();

        $subtotal_price += $total_price;

        //update stock history and product detail stock/qty
        if ($productDetailId !== null) {
          $lastStockHistory = StockHistory::where('id_product_detail', $item['item'])
            ->latest()
            ->first();

          if ($lastStockHistory) {
            // Insert into stock history
            $stock_history = new StockHistory();
            $stock_history->id_product_detail = $item['item'];
            $stock_history->id_transaction = $sale->id;
            $stock_history->movement_type = MovementType::OUT;
            $stock_history->quantity = $item['quantity'];
            $stock_history->stock_before = $lastStockHistory->stock_after;
            $stock_history->stock_after = $lastStockHistory->stock_after - $item['quantity'];
            $stock_history->created_by = Auth::id();
            $stock_history->save();

            $product_detail = ProductDetail::findOrFail($item['item']);
            $product_detail->quantity = $stock_history->stock_after;
            $product_detail->updated_by = Auth::id();
            $product_detail->save();
          }
        }
      }

      $findSale = Sale::find($sale->id); // Fetch the Sale instance from the database
      $findSale->subtotal_price = $subtotal_price; // Update the subtotal price
      $findSale->final_price = $subtotal_price - $findSale->discount;
      $findSale->save();

      DB::commit();

      // Redirect or respond with success message
      return redirect()
        ->route('transaction-sale')
        ->with('success', 'Sale created successfully.');
    } catch (ValidationException $e) {
      DB::rollBack();
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      DB::rollBack();
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', $e->getMessage());
    }
  }

  public function getById($id)
  {
    $customers = Customer::where('is_active', true)->pluck('name', 'id');
    $products = DB::table('product_details')
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->where('p.is_active', true);

    $services = DB::table('services')
      ->selectRaw('CONCAT("jasa-", id) as id, name')
      ->where('is_active', true);

    $unionQuery = $products->union($services);

    $items = DB::table(DB::raw("({$unionQuery->toSql()}) as sub"))
      ->mergeBindings($unionQuery)
      ->orderBy('name')
      ->pluck('name', 'id');

    $sale = Sale::findOrFail($id);
    $saleDetails = SaleDetail::where('id_sale', $id)->get();

    foreach ($saleDetails as $saleDetail) {
      $quantity = $saleDetail->quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $saleDetail->quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $saleDetail->quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }
    }

    $sale->discount = intval($sale->discount);

    return view('content.transactions.sale-edit', [
      'id' => $id,
      'products' => $items,
      'customers' => $customers,
      'sale' => $sale,
      'saleDetails' => $saleDetails,
    ]);
  }

  public function edit(Request $request, $id)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'id_customer' => 'required|exists:customers,id',
          'discount' => 'required|numeric',
          'bank_account_no' => 'required',
          'status' => 'required|in:belum lunas,lunas',
          'payment_type' => 'required|in:cash,tempo',
          'invoice_no' => 'required|unique:sales,invoice_no,' . $id,
          'group-a' => 'required|array',
          'group-a.*.item' => 'required',
          'group-a.*.quantity' => 'required',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          // 'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          // 'group-a.*.quantity.min' => 'Quantity must be at least 1.',
          'invoice_no.unique' => 'The invoice number has already been taken.',
        ]
      );
      DB::beginTransaction();

      $subtotal_price = 0;

      $sale = Sale::findOrFail($id);
      $sale->date = $validatedData['date'];
      $sale->id_customer = $validatedData['id_customer'];
      $sale->invoice_no = $validatedData['invoice_no'];
      $sale->discount = $validatedData['discount'];
      $sale->bank_account_no = $validatedData['bank_account_no'];
      $sale->technician = $request->technician ?? '';
      $sale->note = $request->note ?? '';
      $sale->status = $validatedData['status'];
      $sale->payment_type = $validatedData['payment_type'];
      $sale->updated_by = Auth::id();
      $sale->save();

      $customer = Customer::find($validatedData['id_customer']);
      foreach ($request->input('group-a') as $item) {
        if (strpos($item['item'], 'jasa-') === 0) {
          $serviceId = substr($item['item'], 5);
          $productDetailId = null;
          $findService = Service::find($serviceId);
          $productPrice = $findService->price;
        } else {
          $findProductDetail = ProductDetail::find($item['item']);
          $serviceId = null;
          $productDetailId = $item['item'];
          $productPrice = 0;

          // Determine the price based on customer type and payment type
          if ($validatedData['payment_type'] === 'cash') {
            if ($customer->type === 'user') {
              $productPrice = $findProductDetail->final_price_user_cash;
            } else {
              $productPrice = $findProductDetail->final_price_toko_cash;
            }
          } else {
            if ($customer->type === 'toko') {
              $productPrice = $findProductDetail->final_price_toko_tempo;
            } else {
              $productPrice = $findProductDetail->final_price_user_tempo;
            }
          }
        }

        // Convert the normalized quantity to a float value
        $item['quantity'] = strtr($item['quantity'], ['.' => '', ',' => '.']);
        $item['quantity'] = floatval($item['quantity']);

        $existingSaleDetail = SaleDetail::where('id_sale', $id)
          ->where('id_product_detail', $productDetailId)
          ->where('id_service', $serviceId)
          ->first();

        if ($existingSaleDetail) {
          // If sale detail exists, check if quantity has changed
          //total price per detail
          $total_price = $item['quantity'] * $existingSaleDetail->price;

          if ($existingSaleDetail->quantity != $item['quantity']) {
            if ($productDetailId !== null) {
              // Calculate quantity difference
              $quantityDifference = $item['quantity'] - $existingSaleDetail->quantity;
              // Update stock history with quantity difference
              $this->updateStockHistory($id, $productDetailId, $quantityDifference);
            }

            // Update sale detail with new quantity
            $existingSaleDetail->quantity = $item['quantity'];
            // $existingSaleDetail->price = $productPrice;
            $existingSaleDetail->total_price = $total_price;
            $existingSaleDetail->updated_by = Auth::id();
            $existingSaleDetail->save();
          }

          // If sale detail exists, check if product price has changed
          // if ($productPrice != $existingSaleDetail->price) {
          //   // Update sale detail with new price
          //   $existingSaleDetail->price = $productPrice;
          //   $existingSaleDetail->total_price = $total_price;
          //   $existingSaleDetail->updated_by = Auth::id();
          //   $existingSaleDetail->save();
          // }
        } else {
          //total price per detail
          $total_price = $item['quantity'] * $productPrice;

          // Sale detail does not exist, create new sale detail
          $saleDetail = new SaleDetail();
          $saleDetail->id_sale = $id;
          $saleDetail->id_product_detail = $productDetailId;
          $saleDetail->id_service = $serviceId;
          $saleDetail->quantity = $item['quantity'];
          $saleDetail->price = $productPrice;
          $saleDetail->total_price = $total_price;
          $saleDetail->created_by = Auth::id();
          $saleDetail->save();

          if ($productDetailId !== null) {
            $this->updateStockHistory($id, $productDetailId, $item['quantity']);
          }
        }

        $subtotal_price += $total_price;
      }

      // Delete sale details for items not present in the request
      $this->deleteMissingSaleDetails($request->input('group-a'), $id);

      $findSale = Sale::find($id); // Fetch the Sale instance from the database
      $findSale->subtotal_price = $subtotal_price; // Update the subtotal price
      $findSale->final_price = $subtotal_price - $validatedData['discount'];
      $findSale->save();

      DB::commit();

      return redirect()
        ->route('transaction-sale')
        ->with('success', 'Sale updated successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      DB::rollBack();
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      DB::rollBack();
      return Redirect::back()->with('othererror', $e->getMessage());
    }
  }

  // Function to update stock history
  private function updateStockHistory($saleId, $productDetailId, $quantity)
  {
    // Find last stock history
    $lastStockHistory = StockHistory::where('id_product_detail', $productDetailId)
      ->latest()
      ->first();

    if ($lastStockHistory) {
      // Insert new stock history
      $stockHistory = new StockHistory();
      $stockHistory->id_product_detail = $productDetailId;
      $stockHistory->id_transaction = $saleId;
      $stockHistory->movement_type = MovementType::OUT;
      $stockHistory->quantity = $quantity;
      $stockHistory->stock_before = $lastStockHistory->stock_after;
      $stockHistory->stock_after = $lastStockHistory->stock_after - $quantity;
      $stockHistory->created_by = Auth::id();
      $stockHistory->save();

      // Update product detail quantity
      $productDetail = ProductDetail::findOrFail($productDetailId);
      $productDetail->quantity = $stockHistory->stock_after;
      $productDetail->updated_by = Auth::id();
      $productDetail->save();
    }
  }

  // Function to delete sale details for missing items (for EDIT process)
  private function deleteMissingSaleDetails($items, $saleId)
  {
    $productDetailIds = [];
    $serviceIds = [];

    // Separate product detail IDs and service IDs
    foreach ($items as $item) {
      if (strpos($item['item'], 'jasa-') === 0) {
        $serviceIds[] = substr($item['item'], 5); // Extract numeric ID from 'jasa-'
      } else {
        $productDetailIds[] = $item['item'];
      }
    }

    // $existingProductDetailIds = array_map(function ($item) {
    //   return $item['item'];
    // }, $items);

    // Find sale details with product details that need to be deleted
    $productDetailsToDelete = SaleDetail::where('id_sale', $saleId)
      ->whereNotIn('id_product_detail', $productDetailIds)
      ->whereNull('id_service')
      ->get();

    // Find sale details with services that need to be deleted
    $servicesToDelete = SaleDetail::where('id_sale', $saleId)
      ->whereNotIn('id_service', $serviceIds)
      ->whereNull('id_product_detail')
      ->get();

    // Delete sale details for product details and update stock history
    foreach ($productDetailsToDelete as $saleDetail) {
      $this->updateStockHistory($saleId, $saleDetail->id_product_detail, -$saleDetail->quantity);
      $saleDetail->delete();
    }

    // Delete sale details for services (no stock history update needed)
    foreach ($servicesToDelete as $saleDetail) {
      $saleDetail->delete();
    }
  }

  public function delete($id)
  {
    try {
      DB::beginTransaction();

      // Retrieve sale details before deletion
      $saleDetails = SaleDetail::where('id_sale', $id)->get();

      SaleDetail::where('id_sale', $id)->delete();

      $sale = Sale::findOrFail($id);
      $sale->delete();

      // Update stock history for each sale detail
      foreach ($saleDetails as $saleDetail) {
        if ($saleDetail->id_product_detail !== null) {
          $this->updateStockHistory($id, $saleDetail->id_product_detail, -$saleDetail->quantity);
        }
      }

      DB::commit();
      return response()->json(['message' => 'Sale deleted successfully'], 200);
    } catch (\Exception $e) {
      // Exceptions (e.g., database errors)
      DB::rollBack();
      return response()->json(['message' => $e->getMessage()], 200);
    }
  }

  public function preview($id)
  {
    $sale = Sale::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($sale->date));
    $customer = Customer::findOrFail($sale->id_customer);
    $saleDetails = SaleDetail::where('id_sale', $id)
      ->selectRaw(
        // 'CONCAT(p.name, " - ", sizes.code) as product_name, pd.code as product_code,
        // sale_details.quantity as sale_quantity, uoms.code as product_uom, sale_details.price, sale_details.total_price'
        'CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN CONCAT(p.name, " - ", sizes.code)
            ELSE services.name
         END as product_name,
         CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN pd.code
            ELSE "-"
         END as product_code,
         sale_details.quantity as sale_quantity,
         CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN uoms.code
            ELSE "-"
         END as product_uom,
         sale_details.price, sale_details.total_price'
      )
      ->leftJoin('product_details as pd', 'pd.id', 'sale_details.id_product_detail')
      ->leftJoin('sizes', 'sizes.id', 'pd.id_size')
      ->leftJoin('products as p', 'p.id', 'pd.id_product')
      ->leftJoin('uoms', 'uoms.id', 'p.id_uom')
      ->leftJoin('services', 'services.id', 'sale_details.id_service')
      ->get();

    foreach ($saleDetails as $detail) {
      $quantity = $detail->sale_quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->sale_quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->sale_quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }

      $detail->price = 'Rp' . number_format($detail->price, 0, ',', '.');
      $detail->total_price = 'Rp' . number_format($detail->total_price, 0, ',', '.');
    }

    $sale->subtotal_price = 'Rp' . number_format($sale->subtotal_price, 0, ',', '.');
    $sale->discount = 'Rp' . number_format($sale->discount, 0, ',', '.');
    $sale->final_price = 'Rp' . number_format($sale->final_price, 0, ',', '.');

    return view('content.transactions.sale-preview', [
      'id' => $id,
      'sale' => $sale,
      'formattedDate' => $formattedDate,
      'saleDetails' => $saleDetails,
      'customer' => $customer,
    ]);
  }

  public function print($id)
  {
    $sale = Sale::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($sale->date));
    $customer = Customer::findOrFail($sale->id_customer);
    $saleDetails = SaleDetail::where('id_sale', $id)
      ->selectRaw(
        // 'CONCAT(p.name, " - ", sizes.code) as product_name, pd.code as product_code,
        // sale_details.quantity as sale_quantity, uoms.code as product_uom, sale_details.price, sale_details.total_price'
        'CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN CONCAT(p.name, " - ", sizes.code)
            ELSE services.name
         END as product_name,
         CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN pd.code
            ELSE "-"
         END as product_code,
         sale_details.quantity as sale_quantity,
         CASE
            WHEN sale_details.id_product_detail IS NOT NULL THEN uoms.code
            ELSE "-"
         END as product_uom,
         sale_details.price, sale_details.total_price'
      )
      ->leftJoin('product_details as pd', 'pd.id', 'sale_details.id_product_detail')
      ->leftJoin('sizes', 'sizes.id', 'pd.id_size')
      ->leftJoin('products as p', 'p.id', 'pd.id_product')
      ->leftJoin('uoms', 'uoms.id', 'p.id_uom')
      ->leftJoin('services', 'services.id', 'sale_details.id_service')
      ->get();

    foreach ($saleDetails as $detail) {
      $quantity = $detail->sale_quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->sale_quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->sale_quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }

      $detail->price = number_format($detail->price, 0, ',', '.');
      $detail->total_price = number_format($detail->total_price, 0, ',', '.');
    }

    $sale->subtotal_price = number_format($sale->subtotal_price, 0, ',', '.');
    $sale->discount = number_format($sale->discount, 0, ',', '.');
    $sale->final_price = number_format($sale->final_price, 0, ',', '.');

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.transactions.sale-print', [
      'id' => $id,
      'sale' => $sale,
      'formattedDate' => $formattedDate,
      'saleDetails' => $saleDetails,
      'customer' => $customer,
      'pageConfigs' => $pageConfigs,
    ]);
  }

  public function indexBelumLunas()
  {
    $customers = Customer::where('is_active', true)->pluck('name', 'id');
    return view('content.transactions.sale-belum-lunas', ['customers' => $customers]);
  }

  public function printBelumLunas($idCustomer)
  {
    $sales = Sale::query()
      ->leftJoin('customers', 'customers.id', 'sales.id_customer')
      ->selectRaw(
        'sales.invoice_no, DATE_FORMAT(sales.date, "%d %b %Y") as formatted_date, customers.name as customer_name, sales.final_price'
      )
      ->where('sales.status', 'belum lunas')
      ->where('sales.id_customer', $idCustomer)
      ->get();

    foreach ($sales as $sale) {
      $sale->final_price = 'Rp' . number_format($sale->final_price, 0, ',', '.');
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.transactions.sale-belum-lunas-print', [
      'sales' => $sales,
      'pageConfigs' => $pageConfigs,
    ]);
  }

  public function checkInvoiceNo(Request $request)
  {
    $invoiceNo = $request->input('invoice_no');
    $saleId = $request->input('sale_id');

    // Check if the invoice number exists in the database
    $exists = \DB::table('sales')
      ->where('invoice_no', $invoiceNo)
      ->when($saleId, function ($query, $saleId) {
        return $query->where('id', '!=', $saleId);
      })
      ->exists();

    return response()->json([
      'is_unique' => !$exists,
    ]);
  }
}
