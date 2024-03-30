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
    Schema::create('delivery_order_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('id_delivery_order');
      $table->unsignedBigInteger('id_product_detail');
      $table->smallInteger('quantity');
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      // Define foreign key constraints
      $table
        ->foreign('id_delivery_order')
        ->references('id')
        ->on('delivery_orders')
        ->onDelete('restrict');
      $table
        ->foreign('id_product_detail')
        ->references('id')
        ->on('product_details')
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
    Schema::dropIfExists('delivery_order_details');
  }
};
