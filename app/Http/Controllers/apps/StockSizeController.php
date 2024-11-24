<?php

namespace App\Http\Controllers\apps;

use App\Models\Size;
use App\Models\ProductDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class StockSizeController extends Controller
{
  public function index()
  {
    $sizes = Size::where('is_active', true)->pluck('code', 'id');
    return view('content.transactions.stock-size', ['sizes' => $sizes]);
  }

  public function print($size)
  {
    $stockSizes = ProductDetail::query()
      ->leftJoin('products as p', 'product_details.id_product', 'p.id')
      ->leftJoin('patterns as pn', 'p.id_pattern', 'pn.id')
      ->leftJoin('sizes', 'product_details.id_size', 'sizes.id')
      ->leftJoin('uoms', 'p.id_uom', 'uoms.id')
      ->select(
        'p.parent_brand',
        'pn.name as pattern_name',
        'product_details.quantity as product_quantity',
        'product_details.final_price_user_cash',
        'product_details.final_price_user_tempo',
        'product_details.final_price_toko_cash',
        'product_details.final_price_toko_tempo',
        'uoms.code as uom_code'
      )
      ->where('sizes.code', $size)
      ->orderBy('p.parent_brand')
      ->orderBy('pn.name')
      ->get();

    foreach ($stockSizes as $detail) {
      $quantity = $detail->product_quantity;
      // Check if the decimal part is 0 or .00, then format as integer
      if (fmod($quantity, 1) == 0.0) {
        $detail->product_quantity = number_format($quantity, 0, ',', '.');
      } else {
        // Otherwise, keep the decimal places but replace dot with comma
        $detail->product_quantity = rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
      }

      $detail->final_price_user_cash = 'Rp' . number_format($detail->final_price_user_cash, 0, ',', '.');
      $detail->final_price_user_tempo = 'Rp' . number_format($detail->final_price_user_tempo, 0, ',', '.');
      $detail->final_price_toko_cash = 'Rp' . number_format($detail->final_price_toko_cash, 0, ',', '.');
      $detail->final_price_toko_tempo = 'Rp' . number_format($detail->final_price_toko_tempo, 0, ',', '.');
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.transactions.stock-size-print', [
      'size' => $size,
      'stockSizes' => $stockSizes,
      'pageConfigs' => $pageConfigs,
    ]);
  }
}
