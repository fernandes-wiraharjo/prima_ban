<?php

namespace App\Http\Controllers\apps;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
