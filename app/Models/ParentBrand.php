<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentBrand extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'is_active', 'created_by', 'updated_by'];

  public function createdByUser()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updatedByUser()
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
}
