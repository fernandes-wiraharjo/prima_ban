<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
  use HasFactory;

  protected $fillable = ['id_product', 'id_size', 'price', 'quantity', 'is_active', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function product()
  {
    return $this->belongsTo(Product::class, 'id_product');
  }

  public function size()
  {
    return $this->belongsTo(size::class, 'id_size');
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
