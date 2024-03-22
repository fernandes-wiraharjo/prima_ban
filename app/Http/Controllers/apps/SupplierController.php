<?php

namespace App\Http\Controllers\apps;

use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
  public function index()
  {
    return view('content.masters.master-supplier-list');
  }

  public function get(Request $request)
  {
    $query = Supplier::query();

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'pic_name',
      3 => 'is_active',
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
          ->where('name', 'like', $searchValue)
          ->orWhere('pic_name', 'like', $searchValue)
          ->orWhere('address', 'like', $searchValue)
          ->orWhere('phone_no', 'like', $searchValue)
          ->orWhere('bank_account_no', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $suppliers = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $suppliers,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'name' => 'required|max:50',
        'address' => 'required|max:250',
        'phone_no' => 'required|max:30',
        'pic_name' => 'required|max:50',
        'bank_account_no' => 'required|max:20',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new supplier instance
      $supplier = new Supplier();
      $supplier->name = $validatedData['name'];
      $supplier->address = $validatedData['address'];
      $supplier->phone_no = $validatedData['phone_no'];
      $supplier->pic_name = $validatedData['pic_name'];
      $supplier->bank_account_no = $validatedData['bank_account_no'];
      $supplier->is_active = $validatedData['is_active'];
      $supplier->created_by = Auth::id();
      $supplier->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Supplier created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the supplier.');
    }
  }

  public function getById($id)
  {
    $supplier = Supplier::findOrFail($id);
    return response()->json($supplier);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|max:50',
      'address' => 'required|max:250',
      'phone_no' => 'required|max:30',
      'pic_name' => 'required|max:50',
      'bank_account_no' => 'required|max:20',
      'is_active' => 'required|in:0,1',
    ]);

    $supplier = Supplier::findOrFail($id);
    $supplier->name = $validatedData['name'];
    $supplier->name = $validatedData['name'];
    $supplier->address = $validatedData['address'];
    $supplier->phone_no = $validatedData['phone_no'];
    $supplier->pic_name = $validatedData['pic_name'];
    $supplier->bank_account_no = $validatedData['bank_account_no'];
    $supplier->is_active = $validatedData['is_active'];
    $supplier->updated_by = Auth::id();
    $supplier->save();

    return redirect()
      ->route('master-supplier')
      ->with('success', 'Supplier updated successfully.');
  }

  public function delete($id)
  {
    // Find the Supplier by ID
    $supplier = Supplier::findOrFail($id);

    // Delete the supplier
    $supplier->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Supplier deleted successfully'], 200);
  }
}
