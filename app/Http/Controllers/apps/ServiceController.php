<?php

namespace App\Http\Controllers\apps;

use App\Models\Service;
use App\Models\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
  public function index()
  {
    return view('content.masters.master-service-list');
  }

  public function get(Request $request)
  {
    $query = Service::query();

    $sortableColumns = [
      0 => '',
      1 => 'name',
      2 => 'price',
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
        $query->where('name', 'like', $searchValue)->orWhere('price', 'like', $searchValue);
      });
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $services = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $services,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'name' => 'required',
        'price' => 'required',
        'is_active' => 'required|in:0,1',
      ]);

      $price = str_replace('.', '', $validatedData['price']);

      // Create a new data instance
      $data = new Service();
      $data->name = $validatedData['name'];
      $data->price = $price;
      $data->is_active = $validatedData['is_active'];
      $data->created_by = Auth::id();
      $data->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'Service created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the service.');
    }
  }

  public function getById($id)
  {
    $service = Service::findOrFail($id);
    return response()->json($service);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|max:50',
      'price' => 'required',
      'is_active' => 'required|in:0,1',
    ]);

    $price = str_replace('.', '', $validatedData['price']);

    $data = Service::findOrFail($id);
    $data->name = $validatedData['name'];
    $data->price = $price;
    $data->is_active = $validatedData['is_active'];
    $data->updated_by = Auth::id();
    $data->save();

    return redirect()
      ->route('master-service')
      ->with('success', 'Service updated successfully.');
  }

  public function delete($id)
  {
    $relatedSaleDetail = SaleDetail::where('id_service', $id)->exists();
    if ($relatedSaleDetail) {
      return response()->json(['message' => 'Cannot delete service as it has associated sale detail.'], 200);
    }

    // Find the data by ID
    $service = Service::findOrFail($id);

    // Delete the data
    $service->delete();

    // Return a response indicating success
    return response()->json(['message' => 'Service deleted successfully'], 200);
  }
}
