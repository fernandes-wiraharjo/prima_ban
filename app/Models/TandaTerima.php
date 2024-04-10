<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerima extends Model
{
  use HasFactory;

  protected $table = 'tanda_terima';
  protected $fillable = ['date', 'total_price', 'receiver_name', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function tandaTerimaDetails()
  {
    return $this->hasMany(TandaTerimaDetail::class, 'id_tanda_terima');
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
