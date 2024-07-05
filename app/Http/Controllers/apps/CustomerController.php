<?php

namespace App\Http\Controllers\apps;

use App\Models\Customer;
use App\Models\Sales;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
  // public function index()
  // {
  //   return view('content.apps.app-user-list');
  // }

  public function index()
  {
    return view('content.masters.master-customer-list');
  }

  public function get(Request $request)
  {
    $query = Customer::query();

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'type',
      3 => 'pic_name',
      4 => 'is_active',
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
          ->orWhere('type', 'like', $searchValue)
          ->orWhere('pic_name', 'like', $searchValue)
          ->orWhere('address', 'like', $searchValue)
          ->orWhere('phone_no', 'like', $searchValue)
          ->orWhere('bank_account_no', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $customers = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $customers,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'name' => 'required|max:50',
        'type' => 'required|max:10',
        'address' => 'required|max:250',
        'phone_no' => 'required|max:30',
        'pic_name' => 'required|max:50',
        'bank_account_no' => 'required|max:50',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new customer instance
      $customer = new Customer();
      $customer->name = $validatedData['name'];
      $customer->type = $validatedData['type'];
      $customer->address = $validatedData['address'];
      $customer->phone_no = $validatedData['phone_no'];
      $customer->pic_name = $validatedData['pic_name'];
      $customer->bank_account_no = $validatedData['bank_account_no'];
      $customer->is_active = $validatedData['is_active'];
      $customer->created_by = Auth::id();
      $customer->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Customer created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the customer.');
    }
  }

  public function getById($id)
  {
    $customer = Customer::findOrFail($id);
    return response()->json($customer);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|max:50',
      'type' => 'required|max:10',
      'address' => 'required|max:250',
      'phone_no' => 'required|max:30',
      'pic_name' => 'required|max:50',
      'bank_account_no' => 'required|max:50',
      'is_active' => 'required|in:0,1',
    ]);

    $customer = Customer::findOrFail($id);
    $customer->name = $validatedData['name'];
    $customer->type = $validatedData['type'];
    $customer->address = $validatedData['address'];
    $customer->phone_no = $validatedData['phone_no'];
    $customer->pic_name = $validatedData['pic_name'];
    $customer->bank_account_no = $validatedData['bank_account_no'];
    $customer->is_active = $validatedData['is_active'];
    $customer->updated_by = Auth::id();
    $customer->save();

    return redirect()
      ->route('master-customer')
      ->with('success', 'Customer updated successfully.');
  }

  public function delete($id)
  {
    $relatedSales = Sales::where('id_customer', $id)->exists();
    if ($relatedSales) {
      return response()->json(['message' => 'Cannot delete customer as it has associated sales.'], 200);
    }

    // Find the customer by ID
    $customer = Customer::findOrFail($id);

    // Delete the customer
    $customer->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Customer deleted successfully'], 200);
  }
}
