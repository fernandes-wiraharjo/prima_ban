<?php

namespace App\Http\Controllers\apps;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Enums\MovementType;
use App\Models\PurchaseDetail;
use App\Models\StockHistory;
use App\Models\ProductDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
  public function index()
  {
    $suppliers = Supplier::where('is_active', true)->pluck('name', 'id');
    return view('content.transactions.purchase', ['suppliers' => $suppliers]);
  }

  public function get(Request $request)
  {
    $query = Purchase::leftJoin('suppliers', 'purchases.id_supplier', 'suppliers.id')->selectRaw(
      'purchases.*,
      suppliers.name as supplier_name,
      DATE_FORMAT(purchases.date, "%d %b %Y") as formatted_date'
    );

    $sortableColumns = [
      0 => '',
      1 => 'supplier_name',
      2 => 'invoice_no',
      3 => 'formatted_date',
      4 => 'final_price',
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
      $sortColumn = 'formatted_date'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('suppliers.name', 'like', $searchValue)
          ->orWhere('purchases.invoice_no', 'like', $searchValue)
          ->orWhereRaw("DATE_FORMAT(purchases.date, '%d %b %Y') LIKE ?", [$searchValue])
          ->orWhere('purchases.final_price', 'like', $searchValue)
          ->orWhere('purchases.status', 'like', $searchValue);
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

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'id_supplier' => 'required|exists:suppliers,id',
        'date' => 'required',
        'final_price' => 'required',
        'status' => 'required|in:lunas,belum lunas',
      ]);

      $final_price = str_replace('.', '', $validatedData['final_price']);

      // Create a new data instance
      $data = new Purchase();
      $data->id_supplier = $validatedData['id_supplier'];
      $data->invoice_no = $request->invoice_no ?? '-';
      $data->date = $validatedData['date'];
      $data->final_price = $final_price;
      $data->status = $validatedData['status'];
      $data->created_by = Auth::id();
      $data->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Purchase created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the purchase. ' . $e->getMessage());
    }
  }

  public function getById($id)
  {
    $purchase = Purchase::findOrFail($id);
    return response()->json($purchase);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'id_supplier' => 'required|exists:suppliers,id',
      'date' => 'required',
      'final_price' => 'required',
      'status' => 'required|in:lunas,belum lunas',
    ]);

    $final_price = str_replace('.', '', $validatedData['final_price']);

    $data = Purchase::findOrFail($id);
    $data->id_supplier = $validatedData['id_supplier'];
    $data->invoice_no = $request->invoice_no ?? '-';
    $data->date = $validatedData['date'];
    $data->final_price = $final_price;
    $data->status = $validatedData['status'];
    $data->updated_by = Auth::id();
    $data->save();

    return redirect()
      ->route('transaction-purchase')
      ->with('success', 'Purchase updated successfully.');
  }

  public function delete($id)
  {
    $relatedDetails = PurchaseDetail::where('id_purchase', $id)->exists();
    if ($relatedDetails) {
      return response()->json(['message' => 'Cannot delete purchase as it has associated details.'], 200);
    }

    // Find the data by ID
    $purchase = Purchase::findOrFail($id);

    // Delete the data
    $purchase->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Purchase deleted successfully'], 200);
  }

  public function indexDetail($id)
  {
    $productDetails = ProductDetail::leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->where('p.is_active', true)
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as product_detail_name')
      ->pluck('product_detail_name', 'id');
    $purchase = Purchase::leftJoin('suppliers', 'suppliers.id', 'purchases.id_supplier')
      ->select('purchases.invoice_no', 'suppliers.name as supplier_name')
      ->where('purchases.id', $id)
      ->first();
    return view('content.transactions.purchase-detail', [
      'idPurchase' => $id,
      'supplier' => $purchase->supplier_name,
      'invoice' => $purchase->invoice_no ?? '-',
      'productDetails' => $productDetails,
    ]);
  }

  public function getDetail(Request $request, $id)
  {
    $query = PurchaseDetail::leftJoin('product_details', 'purchase_details.id_product_detail', 'product_details.id')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->selectRaw('purchase_details.*, CONCAT(p.name, " - ", sizes.code) as product_detail_name')
      ->where('id_purchase', $id);

    $sortableColumns = [
      0 => '',
      1 => 'product_detail_name',
      2 => 'price',
      3 => 'quantity',
      4 => 'total_price',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'product_detail_name'; // Default to any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->whereRaw('CONCAT(p.name, " - ", sizes.code) LIKE ?', [$searchValue])
          ->orWhere('purchase_details.quantity', 'like', $searchValue)
          ->orWhere('purchase_details.price', 'like', $searchValue)
          ->orWhere('purchase_details.total_price', 'like', $searchValue);
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

  public function addDetail(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'id_purchase' => 'required|exists:purchases,id',
        'id_product_detail' => 'required|exists:product_details,id',
        'price' => 'required',
        'quantity' => 'required',
        'total_price' => 'required',
      ]);

      $price = str_replace('.', '', $validatedData['price']);
      $quantity = strtr($validatedData['quantity'], ['.' => '', ',' => '.']);
      $total_price = str_replace('.', '', $validatedData['total_price']);

      // Convert the normalized quantity to a float value
      $quantity = floatval($quantity);

      // Create a new data instance
      $data = new PurchaseDetail();
      $data->id_purchase = $validatedData['id_purchase'];
      $data->id_product_detail = $validatedData['id_product_detail'];
      $data->price = $price;
      $data->quantity = $quantity;
      $data->total_price = $total_price;
      $data->created_by = Auth::id();
      $data->save();

      $lastStockHistory = StockHistory::where('id_product_detail', $validatedData['id_product_detail'])
        ->latest()
        ->first();

      if ($lastStockHistory) {
        // Insert into stock history
        $stock_history = new StockHistory();
        $stock_history->id_product_detail = $data->id_product_detail;
        $stock_history->id_transaction = $data->id_purchase;
        $stock_history->movement_type = MovementType::IN;
        $stock_history->quantity = $quantity;
        $stock_history->stock_before = $lastStockHistory->stock_after;
        $stock_history->stock_after = $quantity + $lastStockHistory->stock_after;
        $stock_history->created_by = Auth::id();
        $stock_history->save();

        $product_detail = ProductDetail::findOrFail($validatedData['id_product_detail']);
        $product_detail->quantity = $stock_history->stock_after;
        $product_detail->updated_by = Auth::id();
        $product_detail->save();
      }

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Purchase detail created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the purchase detail.');
    }
  }

  public function getDetailById($id)
  {
    $purchaseDetail = PurchaseDetail::findOrFail($id);
    return response()->json($purchaseDetail);
  }

  public function editDetail(Request $request, $id)
  {
    try {
      $validatedData = $request->validate([
        'id_purchase' => 'required|exists:purchases,id',
        'id_product_detail' => 'required|exists:product_details,id',
        'price' => 'required',
        'quantity' => 'required',
        'total_price' => 'required',
      ]);

      $price = str_replace('.', '', $validatedData['price']);
      $quantity = strtr($validatedData['quantity'], ['.' => '', ',' => '.']);
      $total_price = str_replace('.', '', $validatedData['total_price']);

      // Convert the normalized quantity to a float value
      $quantity = floatval($quantity);

      $data = PurchaseDetail::findOrFail($id);
      $data->id_purchase = $validatedData['id_purchase'];
      $data->id_product_detail = $validatedData['id_product_detail'];
      $data->price = $price;
      $data->total_price = $total_price;
      $data->quantity = $quantity;
      $data->updated_by = Auth::id();
      $data->save();

      $stockHistoryByPurchase = StockHistory::where('id_product_detail', $validatedData['id_product_detail'])
        ->where('id_transaction', $validatedData['id_purchase'])
        ->where('movement_type', MovementType::IN)
        ->get();

      if ($stockHistoryByPurchase->isNotEmpty()) {
        $sumQuantity = $stockHistoryByPurchase->sum('quantity');

        if ($sumQuantity != $quantity) {
          $lastStockHistory = StockHistory::where('id_product_detail', $validatedData['id_product_detail'])
            ->latest()
            ->first();

          $stock_history = new StockHistory();
          $stock_history->id_product_detail = $data->id_product_detail;
          $stock_history->id_transaction = $data->id_purchase;
          $stock_history->movement_type = MovementType::IN;
          $stock_history->quantity = $quantity - $sumQuantity;
          $stock_history->stock_before = $lastStockHistory->stock_after;
          $stock_history->stock_after = $lastStockHistory->stock_after + $stock_history->quantity;
          $stock_history->created_by = Auth::id();
          $stock_history->save();

          $product_detail = ProductDetail::findOrFail($validatedData['id_product_detail']);
          $product_detail->quantity = $stock_history->stock_after;
          $product_detail->updated_by = Auth::id();
          $product_detail->save();
        }
      }

      return Redirect::back()->with('success', 'Purchase detail updated successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while updating the purchase detail.');
    }
  }

  public function deleteDetail($id)
  {
    // Find the data by ID
    $purchaseDetail = PurchaseDetail::findOrFail($id);

    // Delete the data
    $purchaseDetail->delete();

    $stockHistoryByPurchase = StockHistory::where('id_product_detail', $purchaseDetail->id_product_detail)
      ->where('id_transaction', $purchaseDetail->id_purchase)
      ->where('movement_type', MovementType::IN)
      ->get();
    $sumOfQuantityToDecrease = $stockHistoryByPurchase->sum('quantity');

    $lastStockHistory = StockHistory::where('id_product_detail', $purchaseDetail->id_product_detail)
      ->latest()
      ->first();

    $stock_history = new StockHistory();
    $stock_history->id_product_detail = $purchaseDetail->id_product_detail;
    $stock_history->id_transaction = $purchaseDetail->id_purchase;
    $stock_history->movement_type = MovementType::IN;
    $stock_history->quantity = 0 - $sumOfQuantityToDecrease;
    $stock_history->stock_before = $lastStockHistory->stock_after;
    $stock_history->stock_after = $lastStockHistory->stock_after - $sumOfQuantityToDecrease;
    $stock_history->created_by = Auth::id();
    $stock_history->save();

    $product_detail = ProductDetail::findOrFail($purchaseDetail->id_product_detail);
    $product_detail->quantity = $stock_history->stock_after;
    $product_detail->updated_by = Auth::id();
    $product_detail->save();

    // Return a response indicating success
    return response()->json(['message' => 'Purchase detail deleted successfully'], 200);
  }
}
