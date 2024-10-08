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
    Schema::create('product_details', function (Blueprint $table) {
      $table->id();
      $table->string('code', 30)->unique();
      $table->unsignedBigInteger('id_product');
      $table->unsignedBigInteger('id_size');
      // $table->decimal('price', 10, 2);
      $table->decimal('price_user_cash', 10, 2);
      $table->string('discount_user_cash', 50);
      $table->decimal('final_price_user_cash', 10, 2);
      $table->decimal('price_user_tempo', 10, 2);
      $table->string('discount_user_tempo', 50);
      $table->decimal('final_price_user_tempo', 10, 2);
      $table->decimal('price_toko_cash', 10, 2);
      $table->string('discount_toko_cash', 50);
      $table->decimal('final_price_toko_cash', 10, 2);
      $table->decimal('price_toko_tempo', 10, 2);
      $table->string('discount_toko_tempo', 50);
      $table->decimal('final_price_toko_tempo', 10, 2);
      $table->smallInteger('quantity');
      $table->boolean('is_active')->default(true);
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      $table->unique(['id_product', 'id_size']);

      // Define foreign key constraints
      $table
        ->foreign('id_product')
        ->references('id')
        ->on('products')
        ->onDelete('restrict');
      $table
        ->foreign('id_size')
        ->references('id')
        ->on('sizes')
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
    Schema::dropIfExists('product_details');
  }
};
