<?php

namespace App\Http\Controllers\apps;

use App\Models\ParentBrand;
use App\Models\Pattern;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ParentBrandController extends Controller
{
  public function index()
  {
    return view('content.masters.master-parent-brand-list');
  }

  public function get(Request $request)
  {
    $query = ParentBrand::query();

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'is_active',
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
        $query->where('name', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $brands = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $brands,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'name' => 'required|max:50',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new brand instance
      $brand = new ParentBrand();
      $brand->name = $validatedData['name'];
      $brand->is_active = $validatedData['is_active'];
      $brand->created_by = Auth::id();
      $brand->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Brand created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the brand.');
    }
  }

  public function getById($id)
  {
    $brand = ParentBrand::findOrFail($id);
    return response()->json($brand);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|max:50',
      'is_active' => 'required|in:0,1',
    ]);

    $brand = ParentBrand::findOrFail($id);
    $brand->name = $validatedData['name'];
    $brand->is_active = $validatedData['is_active'];
    $brand->updated_by = Auth::id();
    $brand->save();

    return redirect()
      ->route('master-parent-brand')
      ->with('success', 'Brand updated successfully.');
  }

  public function delete($name)
  {
    $relatedProduct = Product::where('parent_brand', $name)->exists();
    if ($relatedProduct) {
      return response()->json(['message' => 'Cannot delete brand as it has associated product.'], 200);
    }

    $relatedPattern = Pattern::where('parent_brand', $name)->exists();
    if ($relatedPattern) {
      return response()->json(['message' => 'Cannot delete brand as it has associated pattern.'], 200);
    }

    // Find the Brand by ID
    $brand = ParentBrand::where('name', $name)->first();

    // If brand not found, return a 404 response
    if (!$brand) {
      return response()->json(['message' => 'Brand not found.'], 404);
    }

    // Delete the brand
    $brand->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Brand deleted successfully'], 200);
  }
}
