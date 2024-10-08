<?php

namespace App\Http\Controllers\apps;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class DeliveryOrderController extends Controller
{
  public function index()
  {
    return view('content.transactions.delivery-order');
  }

  public function get(Request $request)
  {
    $query = DeliveryOrder::leftJoin('suppliers', 'delivery_orders.id_supplier', '=', 'suppliers.id')->selectRaw(
      'delivery_orders.*, suppliers.name as supplier_name, DATE_FORMAT(delivery_orders.date, "%d %b %Y") as formatted_date'
    );

    $sortableColumns = [
      0 => '',
      1 => 'id',
      2 => 'formatted_date',
      3 => 'supplier_name',
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
          ->where('suppliers.name', 'like', $searchValue)
          ->orWhere('delivery_orders.id', 'like', $searchValue)
          ->orWhereRaw("DATE_FORMAT(delivery_orders.date, '%d %b %Y') LIKE ?", [$searchValue]);
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
    $suppliers = Supplier::where('is_active', true)->pluck('name', 'id');
    $products = ProductDetail::query()
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->where('p.is_active', true)
      ->orderBy('p.name')
      ->pluck('name', 'id');
    return view('content.transactions.delivery-order-add', ['suppliers' => $suppliers, 'products' => $products]);
  }

  public function add(Request $request)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'id_supplier' => 'required|exists:suppliers,id',
          'group-a' => 'required|array',
          'group-a.*.item' => 'required', // Ensure each item is selected
          'group-a.*.quantity' => 'required',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          // 'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          // 'group-a.*.quantity.min' => 'Quantity must be at least 1.',
        ]
      );

      // Create a new do instance
      $deliveryOrder = new DeliveryOrder();
      $deliveryOrder->id_supplier = $validatedData['id_supplier'];
      $deliveryOrder->date = $validatedData['date'];
      $deliveryOrder->created_by = Auth::id();
      $deliveryOrder->save();

      // Process delivery order details
      foreach ($validatedData['group-a'] as $item) {
        // Convert the normalized quantity to a float value
        $item['quantity'] = strtr($item['quantity'], ['.' => '', ',' => '.']);
        $item['quantity'] = floatval($item['quantity']);

        $deliveryOrderDetail = new DeliveryOrderDetail();
        $deliveryOrderDetail->id_delivery_order = $deliveryOrder->id;
        $deliveryOrderDetail->id_product_detail = $item['item'];
        $deliveryOrderDetail->quantity = $item['quantity'];
        $deliveryOrderDetail->created_by = Auth::id();
        $deliveryOrderDetail->save();
      }

      // Redirect or respond with success message
      return redirect()
        ->route('transaction-delivery-order')
        ->with('success', 'Delivery order created successfully.');
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
    $suppliers = Supplier::where('is_active', true)->pluck('name', 'id');
    $products = ProductDetail::query()
      ->selectRaw('product_details.id, CONCAT(p.name, " - ", sizes.code) as name')
      ->leftJoin('products as p', 'p.id', 'product_details.id_product')
      ->leftJoin('sizes', 'sizes.id', 'product_details.id_size')
      ->where('product_details.is_active', true)
      ->where('p.is_active', true)
      ->orderBy('p.name')
      ->pluck('name', 'id');
    $deliverOrder = DeliveryOrder::findOrFail($id);
    $deliveryOrderDetails = DeliveryOrderDetail::where('id_delivery_order', $id)->get();

    foreach ($deliveryOrderDetails as $deliveryOrderDetail) {
      $quantity = $deliveryOrderDetail->quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $deliveryOrderDetail->quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $deliveryOrderDetail->quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }
    }

    return view('content.transactions.delivery-order-edit', [
      'id' => $id,
      'deliveryOrder' => $deliverOrder,
      'suppliers' => $suppliers,
      'products' => $products,
      'deliveryOrderDetails' => $deliveryOrderDetails,
    ]);
  }

  public function edit(Request $request, $id)
  {
    try {
      // Validate the request
      $validatedData = $request->validate(
        [
          'date' => 'required',
          'id_supplier' => 'required|exists:suppliers,id',
          'group-a' => 'required|array',
          'group-a.*.item' => 'required', // Ensure each item is selected
          'group-a.*.quantity' => 'required',
        ],
        [
          // Custom error messages
          'group-a.required' => 'Please select at least one item.',
          'group-a.*.item.required' => 'Please select at least one item.',
          'group-a.*.quantity.required' => 'Please specify the quantity for each item.',
          // 'group-a.*.quantity.numeric' => 'Quantity must be a number.',
          // 'group-a.*.quantity.min' => 'Quantity must be at least 1.',
        ]
      );

      $data = DeliveryOrder::findOrFail($id);
      $data->date = $validatedData['date'];
      $data->id_supplier = $validatedData['id_supplier'];
      $data->updated_by = Auth::id();
      $data->save();

      DeliveryOrderDetail::where('id_delivery_order', $id)->delete();

      foreach ($validatedData['group-a'] as $item) {
        // Convert the normalized quantity to a float value
        $item['quantity'] = strtr($item['quantity'], ['.' => '', ',' => '.']);
        $item['quantity'] = floatval($item['quantity']);

        $deliveryOrderDetail = new DeliveryOrderDetail();
        $deliveryOrderDetail->id_delivery_order = $id;
        $deliveryOrderDetail->id_product_detail = $item['item'];
        $deliveryOrderDetail->quantity = $item['quantity'];
        $deliveryOrderDetail->created_by = Auth::id();
        $deliveryOrderDetail->save();
      }

      return redirect()
        ->route('transaction-delivery-order')
        ->with('success', 'Delivery order updated successfully.');
    } catch (ValidationException $e) {
      // Validation failed, redirect back with errors
      return Redirect::back()
        ->withErrors($e->validator->errors())
        ->withInput();
    } catch (\Exception $e) {
      // Other exceptions (e.g., database errors)
      return Redirect::back()->with('othererror', 'An error occurred while creating the delivery order.');
    }
  }

  public function delete($id)
  {
    DeliveryOrderDetail::where('id_delivery_order', $id)->delete();
    $deliveryOrder = DeliveryOrder::findOrFail($id);
    $deliveryOrder->delete();
    return response()->json(['message' => 'Delivery order deleted successfully'], 200);
  }

  public function preview($id)
  {
    $deliveryOrder = DeliveryOrder::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($deliveryOrder->date));
    $supplier = Supplier::findOrFail($deliveryOrder->id_supplier);
    $deliveryOrderDetails = DeliveryOrderDetail::query()
      ->selectRaw('CONCAT(p.name, " - ", sizes.code) as name, delivery_order_details.quantity')
      ->leftJoin('product_details as pd', 'pd.id', 'delivery_order_details.id_product_detail')
      ->leftJoin('sizes', 'sizes.id', 'pd.id_size')
      ->leftJoin('products as p', 'p.id', 'pd.id_product')
      ->where('delivery_order_details.id_delivery_order', $id)
      ->orderBy('delivery_order_details.id')
      ->get();

    foreach ($deliveryOrderDetails as $detail) {
      $quantity = $detail->quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }
    }

    return view('content.transactions.delivery-order-preview', [
      'id' => $id,
      'deliveryOrder' => $deliveryOrder,
      'formattedDate' => $formattedDate,
      'supplier' => $supplier,
      'deliveryOrderDetails' => $deliveryOrderDetails,
    ]);
  }

  public function print($id)
  {
    $pageConfigs = ['myLayout' => 'blank'];
    $deliveryOrder = DeliveryOrder::findOrFail($id);
    $formattedDate = date('d M Y', strtotime($deliveryOrder->date));
    $supplier = Supplier::findOrFail($deliveryOrder->id_supplier);
    $deliveryOrderDetails = DeliveryOrderDetail::query()
      ->selectRaw('CONCAT(p.name, " - ", sizes.code) as name, delivery_order_details.quantity')
      ->leftJoin('product_details as pd', 'pd.id', 'delivery_order_details.id_product_detail')
      ->leftJoin('sizes', 'sizes.id', 'pd.id_size')
      ->leftJoin('products as p', 'p.id', 'pd.id_product')
      ->where('delivery_order_details.id_delivery_order', $id)
      ->orderBy('delivery_order_details.id')
      ->get();

    foreach ($deliveryOrderDetails as $detail) {
      $quantity = $detail->quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }
    }

    return view('content.transactions.delivery-order-print', [
      'id' => $id,
      'deliveryOrder' => $deliveryOrder,
      'formattedDate' => $formattedDate,
      'supplier' => $supplier,
      'deliveryOrderDetails' => $deliveryOrderDetails,
      'pageConfigs' => $pageConfigs,
    ]);
  }
}
