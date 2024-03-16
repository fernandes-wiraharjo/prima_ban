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
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('code', 10)->nullable();
      $table->unsignedBigInteger('id_brand');
      $table->unsignedBigInteger('id_pattern');
      $table->unsignedBigInteger('id_uom');
      $table->string('name', 50);
      $table->boolean('is_active')->default(true);
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      // Define foreign key constraints
      $table
        ->foreign('id_brand')
        ->references('id')
        ->on('brands')
        ->onDelete('restrict');
      $table
        ->foreign('id_pattern')
        ->references('id')
        ->on('patterns')
        ->onDelete('restrict');
      $table
        ->foreign('id_uom')
        ->references('id')
        ->on('uoms')
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
    Schema::dropIfExists('products');
  }
};
