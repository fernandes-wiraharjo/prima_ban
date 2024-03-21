<?php

namespace App\Http\Controllers\apps;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class UserList extends Controller
{
  // public function index()
  // {
  //   return view('content.apps.app-user-list');
  // }

  public function index()
  {
    return view('content.masters.master-user-list');
  }

  public function get(Request $request)
  {
    $query = User::query();

    $sortableColumns = [
      0 => '',
      1 => 'username',
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
      $sortColumn = 'username'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $query->where('username', 'like', '%' . $request->search['value'] . '%');
    }

    // Get total filter records count
    $totalFilters = $query->count();

    // Apply pagination
    $users = $query
      ->offset($request->input('start'))
      ->limit($request->input('length'))
      ->orderBy($sortColumn, $sortDirection)
      ->get();

    // Prepare response data
    $responseData = [
      'draw' => $request->input('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFilters,
      'data' => $users,
    ];

    return response()->json($responseData);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate([
        'username' => 'required|unique:users|max:50',
        'password' => 'required',
        'is_active' => 'required|in:0,1',
      ]);

      // Create a new user instance
      $user = new User();
      $user->username = $validatedData['username'];
      $user->password = bcrypt($validatedData['password']); // Hash the password
      $user->is_active = $validatedData['is_active'];
      $user->created_by = Auth::id();
      $user->save();

      // Redirect or respond with success message
      return Redirect::back()->with('success', 'User created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the user.');
    }
  }

  public function getById($id)
  {
    $user = User::findOrFail($id);
    return response()->json($user);
  }

  public function edit(Request $request, $id)
  {
    $validatedData = $request->validate([
      'username' => 'required|unique:users,username,' . $id . '|max:50',
      'password' => 'nullable', // You may adjust validation rules as needed
      'is_active' => 'required|in:0,1',
    ]);

    $user = User::findOrFail($id);
    $user->username = $validatedData['username'];
    if ($request->has('password') && $validatedData['password'] != '') {
      $user->password = bcrypt($validatedData['password']);
    }
    $user->is_active = $validatedData['is_active'];
    $user->updated_by = Auth::id();
    $user->save();

    return redirect()
      ->route('master-user')
      ->with('success', 'User updated successfully.');
  }

  public function delete($id)
  {
    // Find the user by ID
    $user = User::findOrFail($id);

    // Delete the user
    $user->delete();

    // Return a response indicating success
    return response()->json(['message' => 'User deleted successfully'], 200);
  }
}
