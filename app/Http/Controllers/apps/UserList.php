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

  public function get()
  {
    $users = User::all();

    return response()->json($users);
  }
}
