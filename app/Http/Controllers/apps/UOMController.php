<?php

namespace App\Http\Controllers\apps;

use App\Models\UOM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class UOMController extends Controller
{
  public function index()
  {
    return view('content.masters.master-uom-list');
  }

  public function get(Request $request)
  {
    $query = UOM::query();

    $sortableColumns = [
      0 => '',
      1 => 'code',
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
      $sortColumn = 'code'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query->where('code', 'like', $searchValue);
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
        'code' => 'required|max:10',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new brand instance
      $data = new UOM();
      $data->code = $validatedData['code'];
      $data->is_active = $validatedData['is_active'];
      $data->created_by = Auth::id();
      $data->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'UOM created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the uom.');
    }
  }

  public function getById($id)
  {
    $data = UOM::findOrFail($id);
    return response()->json($data);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'code' => 'required|max:10',
      'is_active' => 'required|in:0,1',
    ]);

    $data = UOM::findOrFail($id);
    $data->name = $validatedData['code'];
    $data->is_active = $validatedData['is_active'];
    $data->updated_by = Auth::id();
    $data->save();

    return redirect()
      ->route('master-uom')
      ->with('success', 'uom updated successfully.');
  }

  public function delete($id)
  {
    // Find the data by ID
    $data = UOM::findOrFail($id);

    // Delete the brand
    $data->delete();

    // Return a response indicating success
    return response()->json(['message' => 'UOM deleted successfully'], 200);
  }
}
