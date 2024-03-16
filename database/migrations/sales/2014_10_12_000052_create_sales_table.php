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
    Schema::create('sales', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('id_product_detail');
      $table->unsignedBigInteger('id_customer');
      $table->string('invoice_no', 50)->nullable();
      $table->date('date');
      $table->smallInteger('quantity');
      $table->decimal('price', 10, 2);
      $table->decimal('total_price', 10, 2);
      $table->decimal('discount_percentage', 5, 2);
      $table->decimal('nett_price', 10, 2);
      $table->string('bank_account_no', 30);
      $table->string('status', 30);
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
        ->foreign('id_customer')
        ->references('id')
        ->on('customers')
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
    Schema::dropIfExists('sales');
  }
};
