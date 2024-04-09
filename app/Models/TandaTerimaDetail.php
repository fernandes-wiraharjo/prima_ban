<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerimaDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'id_tanda_terima',
    'invoice_no',
    'invoice_date',
    'invoice_price',
    'invoice_description',
    'created_by',
    'updated_by',
  ];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function tandaTerima()
  {
    return $this->belongsTo(TandaTerima::class, 'id_tanda_terima');
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
