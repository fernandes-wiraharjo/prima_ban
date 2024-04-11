<?php

namespace App\Http\Controllers\apps;

use App\Models\TandaTerima;
use App\Models\TandaTerimaDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class TandaTerimaController extends Controller
{
  public function index()
  {
    return view('content.transactions.tanda-terima');
  }

  public function get(Request $request)
  {
    $query = TandaTerima::query()->selectRaw(
      'tanda_terima.*, DATE_FORMAT(tanda_terima.date, "%d %b %Y") as formatted_date'
    );

    $sortableColumns = [
      0 => '',
      1 => 'id',
      2 => 'formatted_date',
      3 => 'total_price',
      4 => 'receiver_name',
    ];

    // Retrieve the column index and direction from the request
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');

    // Determine the column name based on the column index
    if (isset($sortableColumns[$sortColumnIndex])) {
      $sortColumn = $sortableColumns[$sortColumnIndex];
    } else {
      // Default sorting column if invalid or not provided
      $sortColumn = 'formatted_date'; // Default to 'username' or any other preferred column
    }

    // Get total records count (before filtering)
    $totalRecords = $query->count();

    // Apply search filtering
    if ($request->has('search') && !empty($request->search['value'])) {
      $searchValue = '%' . $request->search['value'] . '%';
      $query->where(function ($query) use ($searchValue) {
        $query
          ->where('total_price', 'like', $searchValue)
          ->orWhere('receiver_name', 'like', $searchValue)
          ->orWhereRaw("DATE_FORMAT(tanda_terima.date, '%d %b %Y') LIKE ?", [$searchValue]);
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

  public function indexAdd()
  {
    return view('content.transactions.tanda-terima-add');
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'receiver_name' => 'required',
          'group-a' => 'required|array',
          'group-a.*.invoice_date' => 'required',
          'group-a.*.invoice_no' => 'required',
          'group-a.*.invoice_price' => 'required|numeric|min:1',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please input at least one item.',
          'group-a.*.invoice_date.required' => 'Please input at least one tanggal faktur.',
          'group-a.*.invoice_no.required' => 'Please input at least one no faktur.',
          'group-a.*.invoice_price.required' => 'Please input at least one nilai faktur.',
          'group-a.*.invoice_price.numeric' => 'Nilai faktur must be a number.',
          'group-a.*.invoice_price.min' => 'Nilai faktur must be at least 1.',
        ]
      );
      $total_price = 0;

      // Create a new tanda terima instance
      $tandaTerima = new TandaTerima();
      $tandaTerima->date = $validatedData['date'];
      $tandaTerima->receiver_name = $validatedData['receiver_name'];
      $tandaTerima->created_by = Auth::id();
      $tandaTerima->total_price = $total_price;

      $tandaTerima->save();

      // Process tanda terima details
      foreach ($request->input('group-a') as $item) {
        $tandaTerimaDetail = new TandaTerimaDetail();
        $tandaTerimaDetail->id_tanda_terima = $tandaTerima->id;
        $tandaTerimaDetail->invoice_no = $item['invoice_no'];
        $tandaTerimaDetail->invoice_date = $item['invoice_date'];
        $tandaTerimaDetail->invoice_price = $item['invoice_price'];
        $tandaTerimaDetail->invoice_description = $item['invoice_description'] ?? '';
        $tandaTerimaDetail->created_by = Auth::id();
        $tandaTerimaDetail->save();

        $total_price += $item['invoice_price'];
      }

      $findTandaTerima = TandaTerima::find($tandaTerima->id); // Fetch the TandaTerima instance from the database
      $findTandaTerima->total_price = $total_price; // Update the total price
      $findTandaTerima->save();

      // Redirect or respond with success message
      return redirect()
        ->route('transaction-tanda-terima')
        ->with('success', 'Tanda terima created successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', $e->getMessage());
    }
  }

  public function getById($id)
  {
    $tandaTerima = TandaTerima::findOrFail($id);
    $tandaTerimaDetails = TandaTerimaDetail::where('id_tanda_terima', $id)->get();

    foreach ($tandaTerimaDetails as $detail) {
      $detail->invoice_price = intval($detail->invoice_price);
    }

    return view('content.transactions.tanda-terima-edit', [
      'id' => $id,
      'tandaTerima' => $tandaTerima,
      'tandaTerimaDetails' => $tandaTerimaDetails,
    ]);
  }

  public function edit(Request $request, $id)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'receiver_name' => 'required',
          'group-a' => 'required|array',
          'group-a.*.invoice_date' => 'required',
          'group-a.*.invoice_no' => 'required',
          'group-a.*.invoice_price' => 'required|numeric|min:1',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please input at least one item.',
          'group-a.*.invoice_date.required' => 'Please input at least one tanggal faktur.',
          'group-a.*.invoice_no.required' => 'Please input at least one no faktur.',
          'group-a.*.invoice_price.required' => 'Please input at least one nilai faktur.',
          'group-a.*.invoice_price.numeric' => 'Nilai faktur must be a number.',
          'group-a.*.invoice_price.min' => 'Nilai faktur must be at least 1.',
        ]
      );

      TandaTerimaDetail::where('id_tanda_terima', $id)->delete();

      $total_price = 0;
      foreach ($request->input('group-a') as $item) {
        $tandaTerimaDetail = new TandaTerimaDetail();
        $tandaTerimaDetail->id_tanda_terima = $id;
        $tandaTerimaDetail->invoice_no = $item['invoice_no'];
        $tandaTerimaDetail->invoice_date = $item['invoice_date'];
        $tandaTerimaDetail->invoice_price = $item['invoice_price'];
        $tandaTerimaDetail->invoice_description = $item['invoice_description'] ?? '';
        $tandaTerimaDetail->created_by = Auth::id();
        $tandaTerimaDetail->save();

        $total_price += $item['invoice_price'];
      }

      $tandaTerima = TandaTerima::findOrFail($id);
      $tandaTerima->date = $validatedData['date'];
      $tandaTerima->receiver_name = $validatedData['receiver_name'];
      $tandaTerima->total_price = $total_price;
      $tandaTerima->updated_by = Auth::id();
      $tandaTerima->save();

      return redirect()
        ->route('transaction-tanda-terima')
        ->with('success', 'Tanda terima updated successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', $e->getMessage());
    }
  }

  public function delete($id)
  {
    TandaTerimaDetail::where('id_tanda_terima', $id)->delete();
    $tandaTerima = TandaTerima::findOrFail($id);
    $tandaTerima->delete();
    return response()->json(['message' => 'Tanda terima deleted successfully'], 200);
  }

  public function preview($id)
  {
    $tandaTerima = TandaTerima::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($tandaTerima->date));
    $tandaTerimaDetails = TandaTerimaDetail::where('id_tanda_terima', $id)
      ->selectRaw(
        'tanda_terima_details.*, DATE_FORMAT(tanda_terima_details.invoice_date, "%d %b %Y") as formatted_invoice_date'
      )
      ->get();

    foreach ($tandaTerimaDetails as $detail) {
      $detail->invoice_price = 'Rp' . number_format($detail->invoice_price, 0, ',', '.');
    }

    $tandaTerima->total_price = 'Rp' . number_format($tandaTerima->total_price, 0, ',', '.');

    return view('content.transactions.tanda-terima-preview', [
      'id' => $id,
      'tandaTerima' => $tandaTerima,
      'formattedDate' => $formattedDate,
      'tandaTerimaDetails' => $tandaTerimaDetails,
    ]);
  }

  public function print($id)
  {
    $tandaTerima = TandaTerima::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($tandaTerima->date));
    $tandaTerimaDetails = TandaTerimaDetail::where('id_tanda_terima', $id)
      ->selectRaw(
        'tanda_terima_details.*, DATE_FORMAT(tanda_terima_details.invoice_date, "%d %b %Y") as formatted_invoice_date'
      )
      ->get();

    foreach ($tandaTerimaDetails as $detail) {
      $detail->invoice_price = 'Rp' . number_format($detail->invoice_price, 0, ',', '.');
    }

    $tandaTerima->total_price = 'Rp' . number_format($tandaTerima->total_price, 0, ',', '.');

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.transactions.tanda-terima-print', [
      'id' => $id,
      'tandaTerima' => $tandaTerima,
      'formattedDate' => $formattedDate,
      'tandaTerimaDetails' => $tandaTerimaDetails,
      'pageConfigs' => $pageConfigs,
    ]);
  }
}
