<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  use HasFactory;

  protected $table = 'sales';
  protected $fillable = [
    'id_customer',
    'invoice_no',
    'date',
    'subtotal_price',
    'discount',
    'final_price',
    'bank_account_no',
    'status',
    'created_by',
    'updated_by',
  ];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function saleDetails()
  {
    return $this->hasMany(SaleDetail::class, 'id_sale');
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
