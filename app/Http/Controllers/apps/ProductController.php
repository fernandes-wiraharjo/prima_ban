<?php

namespace App\Http\Controllers\apps;

use App\Enums\MovementType;
use App\Models\Brand;
use App\Models\DeliveryOrderDetail;
use App\Models\PurchaseDetail;
use App\Models\SaleDetail;
use App\Models\StockHistory;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Size;
use App\Models\UOM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
  public function index()
  {
    $brands = Brand::where('is_active', true)->pluck('name', 'id');
    $uoms = UOM::where('is_active', true)->pluck('code', 'id');
    return view('content.masters.master-product-list', ['brands' => $brands, 'uoms' => $uoms]);
  }

  public function get(Request $request)
  {
    $query = Product::leftJoin('brands', 'products.id_brand', '=', 'brands.id')
      ->leftJoin('patterns', 'products.id_pattern', '=', 'patterns.id')
      ->leftJoin('uoms', 'products.id_uom', '=', 'uoms.id')
      ->select(
        'products.*',
        'patterns.parent_brand',
        'brands.name as brand_name',
        'patterns.name as pattern_name',
        'uoms.code as uom_name'
      );

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'parent_brand',
      3 => 'brand_name',
      4 => 'pattern_name',
      5 => 'uom_name',
      6 => 'is_active',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'name'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('products.name', 'like', $searchValue)
          ->orWhere('parent_brand', 'like', $searchValue)
          ->orWhere('brands.name', 'like', $searchValue)
          ->orWhere('patterns.name', 'like', $searchValue)
          ->orWhere('uoms.code', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $products = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $products,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'parent_brand' => 'required|in:Bridgestone,GT',
        'id_brand' => 'required|exists:brands,id',
        'id_pattern' => 'required|exists:patterns,id',
        'id_uom' => 'required|exists:uoms,id',
        'name' => 'required|max:50',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new data instance
      $data = new Product();
      $data->parent_brand = $validatedData['parent_brand'];
      $data->id_brand = $validatedData['id_brand'];
      $data->id_pattern = $validatedData['id_pattern'];
      $data->id_uom = $validatedData['id_uom'];
      $data->name = $validatedData['name'];
      $data->is_active = $validatedData['is_active'];
      $data->created_by = Auth::id();
      $data->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Product created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the product.');
    }
  }

  public function getById($id)
  {
    $product = Product::findOrFail($id);
    return response()->json($product);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'parent_brand' => 'required|in:Bridgestone,GT',
      'id_brand' => 'required|exists:brands,id',
      'id_pattern' => 'required|exists:patterns,id',
      'id_uom' => 'required|exists:uoms,id',
      'name' => 'required|max:50',
      'is_active' => 'required|in:0,1',
    ]);

    $data = Product::findOrFail($id);
    $data->parent_brand = $validatedData['parent_brand'];
    $data->id_brand = $validatedData['id_brand'];
    $data->id_pattern = $validatedData['id_pattern'];
    $data->id_uom = $validatedData['id_uom'];
    $data->name = $validatedData['name'];
    $data->is_active = $validatedData['is_active'];
    $data->updated_by = Auth::id();
    $data->save();

    return redirect()
      ->route('master-product')
      ->with('success', 'Product updated successfully.');
  }

  public function delete($id)
  {
    $relatedDetails = ProductDetail::where('id_product', $id)->exists();
    if ($relatedDetails) {
      return response()->json(['message' => 'Cannot delete product as it has associated details.'], 200);
    }

    // Find the data by ID
    $product = Product::findOrFail($id);

    // Delete the data
    $product->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Product deleted successfully'], 200);
  }

  public function indexDetail($id, $name)
  {
    $sizes = Size::where('is_active', true)->pluck('code', 'id');
    return view('content.masters.master-product-detail-list', ['idProduct' => $id, 'name' => $name, 'sizes' => $sizes]);
  }

  public function getDetail(Request $request, $id)
  {
    $query = ProductDetail::leftJoin('sizes', 'product_details.id_size', 'sizes.id')
      ->select('product_details.*', 'sizes.code as size_name')
      ->where('id_product', $id);

    $sortableColumns = [
      0 => '',
      1 => 'size_name',
      2 => 'final_price_user_cash',
      3 => 'final_price_user_tempo',
      4 => 'final_price_toko_cash',
      5 => 'final_price_toko_tempo',
      6 => 'quantity',
      7 => 'is_active',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'size_name'; // Default to any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('sizes.code', 'like', $searchValue)
          ->orWhere('product_details.quantity', 'like', $searchValue)
          ->orWhere('product_details.final_price_user_cash', 'like', $searchValue)
          ->orWhere('product_details.final_price_user_tempo', 'like', $searchValue)
          ->orWhere('product_details.final_price_toko_cash', 'like', $searchValue)
          ->orWhere('product_details.final_price_toko_tempo', 'like', $searchValue);
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

  public function addProductDetail(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'id_product' => 'required|exists:products,id',
        'id_size' => 'required|exists:sizes,id',
        'code' => 'required',
        'price_user_cash' => 'required',
        'discount_user_cash' => 'required',
        'final_price_user_cash' => 'required',
        'price_user_tempo' => 'required',
        'discount_user_tempo' => 'required',
        'final_price_user_tempo' => 'required',
        'price_toko_cash' => 'required',
        'discount_toko_cash' => 'required',
        'final_price_toko_cash' => 'required',
        'price_toko_tempo' => 'required',
        'discount_toko_tempo' => 'required',
        'final_price_toko_tempo' => 'required',
        'quantity' => 'required',
        'is_active' => 'required|in:0,1',
      ]);

      $price_user_cash = str_replace('.', '', $validatedData['price_user_cash']);
      $discount_user_cash = str_replace('.', '', $validatedData['discount_user_cash']);
      $final_price_user_cash = str_replace('.', '', $validatedData['final_price_user_cash']);
      $price_user_tempo = str_replace('.', '', $validatedData['price_user_tempo']);
      $discount_user_tempo = str_replace('.', '', $validatedData['discount_user_tempo']);
      $final_price_user_tempo = str_replace('.', '', $validatedData['final_price_user_tempo']);
      $price_toko_cash = str_replace('.', '', $validatedData['price_toko_cash']);
      $discount_toko_cash = str_replace('.', '', $validatedData['discount_toko_cash']);
      $final_price_toko_cash = str_replace('.', '', $validatedData['final_price_toko_cash']);
      $price_toko_tempo = str_replace('.', '', $validatedData['price_toko_tempo']);
      $discount_toko_tempo = str_replace('.', '', $validatedData['discount_toko_tempo']);
      $final_price_toko_tempo = str_replace('.', '', $validatedData['final_price_toko_tempo']);
      $quantity = str_replace('.', '', $validatedData['quantity']);

      // Create a new data instance
      $data = new ProductDetail();
      $data->id_product = $validatedData['id_product'];
      $data->id_size = $validatedData['id_size'];
      $data->code = $validatedData['code'];
      $data->price_user_cash = $price_user_cash;
      $data->discount_user_cash = $discount_user_cash;
      $data->final_price_user_cash = $final_price_user_cash;
      $data->price_user_tempo = $price_user_tempo;
      $data->discount_user_tempo = $discount_user_tempo;
      $data->final_price_user_tempo = $final_price_user_tempo;
      $data->price_toko_cash = $price_toko_cash;
      $data->discount_toko_cash = $discount_toko_cash;
      $data->final_price_toko_cash = $final_price_toko_cash;
      $data->price_toko_tempo = $price_toko_tempo;
      $data->discount_toko_tempo = $discount_toko_tempo;
      $data->final_price_toko_tempo = $final_price_toko_tempo;
      $data->quantity = $quantity;
      $data->is_active = $validatedData['is_active'];
      $data->created_by = Auth::id();
      $data->save();

      $stock_history = new StockHistory();
      $stock_history->id_product_detail = $data->id;
      $stock_history->movement_type = MovementType::IN;
      $stock_history->quantity = $quantity;
      $stock_history->stock_before = 0;
      $stock_history->stock_after = $quantity;
      $stock_history->created_by = Auth::id();
      $stock_history->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Product detail created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the product detail.');
    }
  }

  public function getDetailById($id)
  {
    $productDetail = ProductDetail::findOrFail($id);
    return response()->json($productDetail);
  }

  public function editProductDetail(Request $request, $id)
  {
    try {
      $validatedData = $request->validate([
        'id_size' => 'required|exists:sizes,id',
        'code' => 'required',
        'price_user_cash' => 'required',
        'discount_user_cash' => 'required',
        'final_price_user_cash' => 'required',
        'price_user_tempo' => 'required',
        'discount_user_tempo' => 'required',
        'final_price_user_tempo' => 'required',
        'price_toko_cash' => 'required',
        'discount_toko_cash' => 'required',
        'final_price_toko_cash' => 'required',
        'price_toko_tempo' => 'required',
        'discount_toko_tempo' => 'required',
        'final_price_toko_tempo' => 'required',
        'quantity' => 'required',
        'is_active' => 'required|in:0,1',
      ]);

      $price_user_cash = str_replace('.', '', $validatedData['price_user_cash']);
      $discount_user_cash = str_replace('.', '', $validatedData['discount_user_cash']);
      $final_price_user_cash = str_replace('.', '', $validatedData['final_price_user_cash']);
      $price_user_tempo = str_replace('.', '', $validatedData['price_user_tempo']);
      $discount_user_tempo = str_replace('.', '', $validatedData['discount_user_tempo']);
      $final_price_user_tempo = str_replace('.', '', $validatedData['final_price_user_tempo']);
      $price_toko_cash = str_replace('.', '', $validatedData['price_toko_cash']);
      $discount_toko_cash = str_replace('.', '', $validatedData['discount_toko_cash']);
      $final_price_toko_cash = str_replace('.', '', $validatedData['final_price_toko_cash']);
      $price_toko_tempo = str_replace('.', '', $validatedData['price_toko_tempo']);
      $discount_toko_tempo = str_replace('.', '', $validatedData['discount_toko_tempo']);
      $final_price_toko_tempo = str_replace('.', '', $validatedData['final_price_toko_tempo']);
      $quantity = str_replace('.', '', $validatedData['quantity']);

      $data = ProductDetail::findOrFail($id);
      $data->id_size = $validatedData['id_size'];
      $data->code = $validatedData['code'];
      $data->price_user_cash = $price_user_cash;
      $data->discount_user_cash = $discount_user_cash;
      $data->final_price_user_cash = $final_price_user_cash;
      $data->price_user_tempo = $price_user_tempo;
      $data->discount_user_tempo = $discount_user_tempo;
      $data->final_price_user_tempo = $final_price_user_tempo;
      $data->price_toko_cash = $price_toko_cash;
      $data->discount_toko_cash = $discount_toko_cash;
      $data->final_price_toko_cash = $final_price_toko_cash;
      $data->price_toko_tempo = $price_toko_tempo;
      $data->discount_toko_tempo = $discount_toko_tempo;
      $data->final_price_toko_tempo = $final_price_toko_tempo;
      $data->quantity = $quantity;
      $data->is_active = $validatedData['is_active'];
      $data->updated_by = Auth::id();
      $data->save();

      $lastStockHistory = StockHistory::where('id_product_detail', $id)
        ->latest()
        ->first();

      if ($lastStockHistory) {
        if ($lastStockHistory->stock_after != $quantity) {
          if ($lastStockHistory->stock_after < $quantity) {
            $movementType = MovementType::IN;
          } else {
            $movementType = MovementType::OUT;
          }

          // Insert into stock history
          $stockHistory = new StockHistory();
          $stockHistory->id_product_detail = $id;
          $stockHistory->movement_type = $movementType;
          $stockHistory->stock_before = $lastStockHistory ? $lastStockHistory->stock_after : 0;
          $stockHistory->stock_after = $quantity;
          $stockHistory->quantity =
            $movementType === MovementType::IN
              ? $stockHistory->stock_after - $stockHistory->stock_before
              : $stockHistory->stock_before - $stockHistory->stock_after;
          $stockHistory->created_by = Auth::id();
          $stockHistory->save();
        }
      }

      return Redirect::back()->with('success', 'Product detail updated successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while updating the product detail.');
    }
  }

  public function deleteProductDetail($id)
  {
    $relatedDODetail = DeliveryOrderDetail::where('id_product_detail', $id)->exists();
    if ($relatedDODetail) {
      return response()->json(
        ['message' => 'Cannot delete product detail as it has associated delivery order detail.'],
        200
      );
    }

    $relatedPurchaseDetail = PurchaseDetail::where('id_product_detail', $id)->exists();
    if ($relatedPurchaseDetail) {
      return response()->json(['message' => 'Cannot delete product detail as it has associated purchase detail.'], 200);
    }

    $relatedSaleDetail = SaleDetail::where('id_product_detail', $id)->exists();
    if ($relatedSaleDetail) {
      return response()->json(['message' => 'Cannot delete product detail as it has associated sale detail.'], 200);
    }

    $relatedStockHistory = StockHistory::where('id_product_detail', $id)->exists();
    if ($relatedStockHistory) {
      return response()->json(['message' => 'Cannot delete product detail as it has associated stock history.'], 200);
    }

    // Find the data by ID
    $productDetail = ProductDetail::findOrFail($id);

    // Delete the data
    $productDetail->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Product detail deleted successfully'], 200);
  }
}
