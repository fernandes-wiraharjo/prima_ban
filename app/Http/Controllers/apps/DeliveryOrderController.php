<?php

namespace App\Http\Controllers\apps;

use App\Models\DeliveryOrder;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class DeliveryOrderController extends Controller
{
  public function index()
  {
    return view('content.transactions.delivery-order');
  }

  public function get(Request $request)
  {
    $query = DeliveryOrder::leftJoin('suppliers', 'delivery_orders.id_supplier', '=', 'suppliers.id')->select(
      'delivery_orders.*',
      'suppliers.name as supplier_name'
    );

    $sortableColumns = [
      0 => '',
      1 => 'id',
      2 => 'date',
      3 => 'supplier_name',
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
        $query->where('suppliers.name', 'like', $searchValue);
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
    $suppliers = Supplier::where('is_active', true)->pluck('name', 'id');
    $products = ProductDetail::query()
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->pluck('name', 'id');
    return view('content.transactions.delivery-order-add', ['suppliers' => $suppliers, 'products' => $products]);
  }

  // public function add(Request $request)
  // {
  //   try {
  //     // Validate the request
  //     $validatedData = $request->validate([
  //       'id_brand' => 'required|exists:brands,id',
  //       'name' => 'required|max:50',
  //       'is_active' => 'required|in:0,1',
  //     ]);

  //     // Create a new data instance
  //     $data = new Pattern();
  //     $data->id_brand = $validatedData['id_brand'];
  //     $data->name = $validatedData['name'];
  //     $data->is_active = $validatedData['is_active'];
  //     $data->created_by = Auth::id();
  //     $data->save();

  //     // Redirect or respond with success message
  //     return Redirect::back()->with('success', 'Pattern created successfully.');
  //   } catch (ValidationException $e) {
  //     // Validation failed, redirect back with errors
  //     return Redirect::back()
  //       ->withErrors($e->validator->errors())
  //       ->withInput();
  //   } catch (\Exception $e) {
  //     // Other exceptions (e.g., database errors)
  //     return Redirect::back()->with('othererror', 'An error occurred while creating the pattern.');
  //   }
  // }

  // public function getById($id)
  // {
  //   $pattern = Pattern::findOrFail($id);
  //   return response()->json($pattern);
  // }

  // public function getByBrandId($id)
  // {
  //   $brand = Brand::findOrFail($id);
  //   $patterns = $brand->patterns;
  //   return response()->json($patterns);
  // }

  // public function edit(Request $request, $id)
  // {
  //   $validatedData = $request->validate([
  //     'id_brand' => 'required|exists:brands,id',
  //     'name' => 'required|max:50',
  //     'is_active' => 'required|in:0,1',
  //   ]);

  //   $data = Pattern::findOrFail($id);
  //   $data->id_brand = $validatedData['id_brand'];
  //   $data->name = $validatedData['name'];
  //   $data->is_active = $validatedData['is_active'];
  //   $data->updated_by = Auth::id();
  //   $data->save();

  //   return redirect()
  //     ->route('master-pattern')
  //     ->with('success', 'Pattern updated successfully.');
  // }

  // public function delete($id)
  // {
  //   $relatedProduct = Product::where('id_pattern', $id)->exists();
  //   if ($relatedProduct) {
  //     return response()->json(['message' => 'Cannot delete pattern as it has associated product.'], 200);
  //   }

  //   // Find the data by ID
  //   $pattern = Pattern::findOrFail($id);

  //   // Delete the brand
  //   $pattern->delete();

  //   // Return a response indicating success
  //   return response()->json(['message' => 'Pattern deleted successfully'], 200);
  // }
}
