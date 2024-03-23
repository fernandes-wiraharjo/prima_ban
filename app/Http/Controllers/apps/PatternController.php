<?php

namespace App\Http\Controllers\apps;

use App\Models\Pattern;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
  public function index()
  {
    return view('content.masters.master-pattern-list');
  }

  public function get(Request $request)
  {
    $query = Pattern::query();

    $sortableColumns = [
      0 => '',
      1 => 'brands.name',
      2 => 'patterns.name',
      3 => 'patterns.is_active',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'brands.name'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query->where('name', 'like', $searchValue)->orWhereHas('brand', function ($query) use ($searchValue) {
          $query->where('name', 'like', $searchValue);
        });
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $patterns = $query
      ->with('brand') // Eager load the related Brand model
      ->orderBy($sortColumn, $sortDirection)
      ->paginate($request->input('length'));

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $patterns->items(),
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
      $brand = new Brand();
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
    $brand = Brand::findOrFail($id);
    return response()->json($brand);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|max:50',
      'is_active' => 'required|in:0,1',
    ]);

    $brand = Brand::findOrFail($id);
    $brand->name = $validatedData['name'];
    $brand->is_active = $validatedData['is_active'];
    $brand->updated_by = Auth::id();
    $brand->save();

    return redirect()
      ->route('master-brand')
      ->with('success', 'Brand updated successfully.');
  }

  public function delete($id)
  {
    // Find the Brand by ID
    $brand = Brand::findOrFail($id);

    // Delete the brand
    $brand->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Brand deleted successfully'], 200);
  }
}
