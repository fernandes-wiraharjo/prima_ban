<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pattern extends Model
{
  use HasFactory;

  protected $fillable = ['parent_brand', 'id_brand', 'name', 'is_active', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function brand()
  {
    return $this->belongsTo(Brand::class, 'id_brand');
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
