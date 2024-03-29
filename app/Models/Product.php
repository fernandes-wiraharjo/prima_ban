<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;

  protected $fillable = ['id_brand', 'id_pattern', 'id_uom', 'name', 'is_active', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function brand()
  {
    return $this->belongsTo(Brand::class, 'id_brand');
  }

  public function pattern()
  {
    return $this->belongsTo(Pattern::class, 'id_pattern');
  }

  public function uom()
  {
    return $this->belongsTo(UOM::class, 'id_uom');
  }

  public function productDetail()
  {
    return $this->hasMany(ProductDetail::class, 'id_product');
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
