<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  use HasFactory;

  protected $fillable = ['id_supplier', 'invoice_no', 'date', 'final_price', 'status', 'created_by', 'updated_by'];

  // Define any relationships here, e.g., createdBy, updatedBy
  public function supplier()
  {
    return $this->belongsTo(Supplier::class, 'id_supplier');
  }

  public function purchaseDetail()
  {
    return $this->hasMany(PurchaseDetail::class, 'id_purchase');
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
