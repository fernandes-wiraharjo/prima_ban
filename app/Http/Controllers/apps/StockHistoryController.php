<?php

namespace App\Http\Controllers\apps;

use App\Models\StockHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class StockHistoryController extends Controller
{
  public function index()
  {
    return view('content.transactions.transaction-stock-history');
  }

  public function get(Request $request)
  {
    $query = StockHistory::query()
      ->selectRaw(
        'stock_histories.movement_type, stock_histories.quantity, stock_histories.stock_before, stock_histories.id_transaction, ' .
          'stock_histories.stock_after, DATE_FORMAT(stock_histories.created_at, "%d %b %Y %H:%i:%s") as formatted_created_at, ' .
          'CONCAT(prd.name, " - ", sizes.code) as product_detail'
      )
      ->join('product_details as pd', 'pd.id', 'stock_histories.id_product_detail')
      ->join('products as prd' . 'prd.id', 'pd.id_product')
      ->join('sizes', 'sizes.id', 'pd.id_size');

    $sortableColumns = [
      0 => '',
      1 => 'formatted_created_at',
      2 => 'product_detail',
      3 => 'movement_type',
      4 => 'quantity',
      5 => 'stock_before',
      6 => 'stock_after',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'formatted_created_at'; // Default to 'created at' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('movement_type', 'like', $searchValue)
          ->orWhere('stock_histories.quantity', 'like', $searchValue)
          ->orWhere('stock_histories.stock_before', 'like', $searchValue)
          ->orWhere('stock_histories.stock_after', 'like', $searchValue)
          ->orWhereRaw("CONCAT(prd . name, ' - ', sizes . code) LIKE ?", [$searchValue])
          ->orWhereRaw("DATE_FORMAT(stock_purchases.created_at, '%d %b %Y %H:%i:%s') LIKE ?", [$searchValue]);
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
}
