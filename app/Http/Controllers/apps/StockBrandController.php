<?php

namespace App\Http\Controllers\apps;

use App\Models\ParentBrand;
use App\Models\ProductDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class StockBrandController extends Controller
{
  public function index()
  {
    $parentBrands = ParentBrand::where('is_active', true)->pluck('name', 'id');
    return view('content.transactions.stock-brand', ['parentBrands' => $parentBrands]);
  }

  public function print($brand)
  {
    $stockBrands = ProductDetail::query()
      ->leftJoin('products as p', 'product_details.id_product', 'p.id')
      ->leftJoin('patterns as pn', 'p.id_pattern', 'pn.id')
      ->leftJoin('sizes', 'product_details.id_size', 'sizes.id')
      ->leftJoin('uoms', 'p.id_uom', 'uoms.id')
      ->select(
        'pn.name as pattern_name',
        'product_details.code as product_code',
        'sizes.code as size_code',
        'product_details.quantity as product_quantity',
        'uoms.code as uom_code'
      )
      ->where('p.parent_brand', $brand)
      ->orderBy('pattern_name')
      ->get();

    foreach ($stockBrands as $detail) {
      $quantity = $detail->product_quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->product_quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->product_quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.transactions.stock-brand-print', [
      'brand' => $brand,
      'stockBrands' => $stockBrands,
      'pageConfigs' => $pageConfigs,
    ]);
  }
}
