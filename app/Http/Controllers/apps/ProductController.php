<?php

namespace App\Http\Controllers\apps;

use App\Models\Brand;
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
    $brands = Brand::where('is_active', true)->pluck('id', 'name');
    $uoms = UOM::where('is_active', true)->pluck('id', 'code');
    return view('content.masters.master-product-list', ['brands' => $brands, 'uoms' => $uoms]);
  }

  public function get(Request $request)
  {
    $query = Pattern::leftJoin('brands', 'products.id_brand', '=', 'brands.id')
      ->leftJoin('patterns', 'products.id_pattern', '=', 'patterns.id')
      ->leftJoin('uoms', 'products.id_uom', '=', 'uoms.id')
      ->select('products.*', 'brands.name as brand_name', 'patterns.name as pattern_name', 'uoms.name as uom_name');

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'brand_name',
      3 => 'pattern_name',
      4 => 'uom_name',
      5 => 'is_active',
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
          ->orWhere('brands.name', 'like', $searchValue)
          ->orWhere('patterns.name', 'like', $searchValue)
          ->orWhere('uoms.name', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $patterns = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $patterns,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'id_brand' => 'required|exists:brands,id',
        'id_pattern' => 'required|exists:patterns,id',
        'id_uom' => 'required|exists:uoms,id',
        'name' => 'required|max:50',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new data instance
      $data = new Product();
      $data->id_brand = $validatedData['id_brand'];
      $data->name = $validatedData['name'];
      $data->is_active = $validatedData['is_active'];
      $data->created_by = Auth::id();
      $data->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Pattern created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the pattern.');
    }
  }

  public function getById($id)
  {
    $pattern = Pattern::findOrFail($id);
    return response()->json($pattern);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'id_brand' => 'required|exists:brands,id',
      'name' => 'required|max:50',
      'is_active' => 'required|in:0,1',
    ]);

    $data = Pattern::findOrFail($id);
    $data->id_brand = $validatedData['id_brand'];
    $data->name = $validatedData['name'];
    $data->is_active = $validatedData['is_active'];
    $data->updated_by = Auth::id();
    $data->save();

    return redirect()
      ->route('master-pattern')
      ->with('success', 'Pattern updated successfully.');
  }

  public function delete($id)
  {
    // Find the data by ID
    $pattern = Pattern::findOrFail($id);

    // Delete the brand
    $pattern->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Pattern deleted successfully'], 200);
  }
}
