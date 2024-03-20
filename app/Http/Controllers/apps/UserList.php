<?php

namespace App\Http\Controllers\apps;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
    // Validate the request
    $validatedData = $request->validate([
      'username' => 'required|unique:users|max:50',
      'password' => 'required',
      'is_active' => 'required|in:1,2',
    ]);

    // Create a new user instance
    $user = new User();
    $user->username = $validatedData['username'];
    $user->password = bcrypt($validatedData['password']); // Hash the password
    $user->is_active = $validatedData['is_active'];
    $user->save();

    // Redirect or respond with success message
    return Redirect::back()->with('success', 'User created successfully.');
  }
}
