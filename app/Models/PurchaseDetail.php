<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'id_purchase',
    'id_product_detail',
    'price',
    'quantity',
    'total_price',
    'created_by',
    'updated_by',
  ];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function purchase()
  {
    return $this->belongsTo(Purchase::class, 'id_purchase');
  }

  public function productDetail()
  {
    return $this->belongsTo(ProductDetail::class, 'id_product_detail');
  }

  public function createdByUser()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updatedByUser()
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
}
