<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('purchase_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('id_purchase');
      $table->unsignedBigInteger('id_product_detail');
      $table->smallInteger('quantity');
      $table->decimal('price', 10, 2);
      $table->decimal('total_price', 10, 2);
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      // Define foreign key constraints
      $table
        ->foreign('id_product_detail')
        ->references('id')
        ->on('product_details')
        ->onDelete('restrict');
      $table
        ->foreign('id_purchase')
        ->references('id')
        ->on('purchases')
        ->onDelete('restrict');
      $table
        ->foreign('created_by')
        ->references('id')
        ->on('users')
        ->onDelete('restrict');
      $table
        ->foreign('updated_by')
        ->references('id')
        ->on('users')
        ->onDelete('restrict');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('purchase_details');
  }
};
