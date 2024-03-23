<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
  use HasFactory;

  protected $fillable = ['code', 'is_active', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function createdByUser()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updatedByUser()
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
}
