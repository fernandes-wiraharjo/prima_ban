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
    Schema::create('tanda_terima_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('id_tanda_terima');
      $table->string('invoice_no', 50);
      $table->date('invoice_date');
      $table->decimal('invoice_price', 10, 2);
      $table->text('invoice_description');
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
      $table->timestamps();

      // Define foreign key constraints
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
    Schema::dropIfExists('tanda_terima_details');
  }
};
