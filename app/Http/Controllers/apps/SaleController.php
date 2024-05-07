<?php

namespace App\Http\Controllers\apps;

use App\Enums\MovementType;
use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
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
      ->selectRaw('sales.*, DATE_FORMAT(sales.date, "%d %b %Y") as formatted_date, customers.name as customer_name');

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
          ->orWhere('status', 'like', $searchValue);
      });
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
    $customers = Customer::where('is_active', true)->pluck('name', 'id');
    $products = ProductDetail::query()
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->orderBy('p.name')
      ->pluck('name', 'id');
    return view('content.transactions.sale-add', ['customers' => $customers, 'products' => $products]);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'id_customer' => 'required|exists:customers,id',
          'discount' => 'required|numeric|min:1',
          'bank_account_no' => 'required',
          'status' => 'required|in:belum lunas,lunas',
          'payment_type' => 'required|in:cash,tempo',
          'invoice_no' => 'required',
          'group-a' => 'required|array',
          'group-a.*.item' => 'required',
          'group-a.*.quantity' => 'required|numeric|min:1',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          'group-a.*.quantity.min' => 'Quantity must be at least 1.',
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
        $findProductDetail = ProductDetail::find($item['item']);
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

        //total price per detail
        $total_price = $item['quantity'] * $productPrice;

        $saleDetail = new SaleDetail();
        $saleDetail->id_sale = $sale->id;
        $saleDetail->id_product_detail = $item['item'];
        $saleDetail->quantity = $item['quantity'];
        $saleDetail->price = $productPrice;
        $saleDetail->total_price = $total_price;
        $saleDetail->created_by = Auth::id();
        $saleDetail->save();

        $subtotal_price += $total_price;

        //update stock history and product detail stock/qty
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
    $products = ProductDetail::query()
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->orderBy('p.name')
      ->pluck('name', 'id');
    $sale = Sale::findOrFail($id);
    $saleDetails = SaleDetail::where('id_sale', $id)->get();

    $sale->discount = intval($sale->discount);

    return view('content.transactions.sale-edit', [
      'id' => $id,
      'products' => $products,
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
          'discount' => 'required|numeric|min:1',
          'bank_account_no' => 'required',
          'status' => 'required|in:belum lunas,lunas',
          'payment_type' => 'required|in:cash,tempo',
          'invoice_no' => 'required',
          'group-a' => 'required|array',
          'group-a.*.item' => 'required',
          'group-a.*.quantity' => 'required|numeric|min:1',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          'group-a.*.quantity.min' => 'Quantity must be at least 1.',
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
        $findProductDetail = ProductDetail::find($item['item']);
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

        //total price per detail
        $total_price = $item['quantity'] * $productPrice;

        $existingSaleDetail = SaleDetail::where('id_sale', $id)
          ->where('id_product_detail', $item['item'])
          ->first();

        if ($existingSaleDetail) {
          // If sale detail exists, check if quantity has changed
          if ($existingSaleDetail->quantity != $item['quantity']) {
            // Calculate quantity difference
            $quantityDifference = $item['quantity'] - $existingSaleDetail->quantity;

            // Update sale detail with new quantity
            $existingSaleDetail->quantity = $item['quantity'];
            $existingSaleDetail->price = $productPrice;
            $existingSaleDetail->total_price = $total_price;
            $existingSaleDetail->updated_by = Auth::id();
            $existingSaleDetail->save();

            // Update stock history with quantity difference
            $this->updateStockHistory($id, $item['item'], $quantityDifference);
          }
        } else {
          // Sale detail does not exist, create new sale detail
          $saleDetail = new SaleDetail();
          $saleDetail->id_sale = $id;
          $saleDetail->id_product_detail = $item['item'];
          $saleDetail->quantity = $item['quantity'];
          $saleDetail->price = $productPrice;
          $saleDetail->total_price = $total_price;
          $saleDetail->created_by = Auth::id();
          $saleDetail->save();

          $this->updateStockHistory($id, $item['item'], $item['quantity']);
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
    $existingProductDetailIds = array_map(function ($item) {
      return $item['item'];
    }, $items);

    // Find sale details that need to be deleted
    $saleDetailsToDelete = SaleDetail::where('id_sale', $saleId)
      ->whereNotIn('id_product_detail', $existingProductDetailIds)
      ->get();

    // Delete sale details and update stock history
    foreach ($saleDetailsToDelete as $saleDetail) {
      $this->updateStockHistory($saleId, $saleDetail->id_product_detail, -$saleDetail->quantity);
      $saleDetail->delete();
    }
  }

  // public function delete($id)
  // {
  //   TandaTerimaDetail::where('id_tanda_terima', $id)->delete();
  //   $tandaTerima = TandaTerima::findOrFail($id);
  //   $tandaTerima->delete();
  //   return response()->json(['message' => 'Tanda terima deleted successfully'], 200);
  // }

  // public function preview($id)
  // {
  //   $tandaTerima = TandaTerima::findOrFail($id);
  //   $formattedDate = date('d M Y', strtotime($tandaTerima->date));
  //   $tandaTerimaDetails = TandaTerimaDetail::where('id_tanda_terima', $id)
  //     ->selectRaw(
  //       'tanda_terima_details.*, DATE_FORMAT(tanda_terima_details.invoice_date, "%d %b %Y") as formatted_invoice_date'
  //     )
  //     ->get();

  //   foreach ($tandaTerimaDetails as $detail) {
  //     $detail->invoice_price = 'Rp' . number_format($detail->invoice_price, 0, ',', '.');
  //   }

  //   $tandaTerima->total_price = 'Rp' . number_format($tandaTerima->total_price, 0, ',', '.');

  //   return view('content.transactions.tanda-terima-preview', [
  //     'id' => $id,
  //     'tandaTerima' => $tandaTerima,
  //     'formattedDate' => $formattedDate,
  //     'tandaTerimaDetails' => $tandaTerimaDetails,
  //   ]);
  // }

  // public function print($id)
  // {
  //   $tandaTerima = TandaTerima::findOrFail($id);
  //   $formattedDate = date('d M Y', strtotime($tandaTerima->date));
  //   $tandaTerimaDetails = TandaTerimaDetail::where('id_tanda_terima', $id)
  //     ->selectRaw(
  //       'tanda_terima_details.*, DATE_FORMAT(tanda_terima_details.invoice_date, "%d %b %Y") as formatted_invoice_date'
  //     )
  //     ->get();

  //   foreach ($tandaTerimaDetails as $detail) {
  //     $detail->invoice_price = 'Rp' . number_format($detail->invoice_price, 0, ',', '.');
  //   }

  //   $tandaTerima->total_price = 'Rp' . number_format($tandaTerima->total_price, 0, ',', '.');

  //   $pageConfigs = ['myLayout' => 'blank'];
  //   return view('content.transactions.tanda-terima-print', [
  //     'id' => $id,
  //     'tandaTerima' => $tandaTerima,
  //     'formattedDate' => $formattedDate,
  //     'tandaTerimaDetails' => $tandaTerimaDetails,
  //     'pageConfigs' => $pageConfigs,
  //   ]);
  // }
}
