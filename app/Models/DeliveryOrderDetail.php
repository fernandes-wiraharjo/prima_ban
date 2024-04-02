<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderDetail extends Model
{
  use HasFactory;

  protected $fillable = ['id_delivery_order', 'id_product_detail', 'quantity', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function deliveryOrder()
  {
    return $this->belongsTo(DeliveryOrder::class, 'id_delivery_order');
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
