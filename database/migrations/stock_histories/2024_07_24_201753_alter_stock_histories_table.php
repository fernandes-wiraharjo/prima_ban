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
    Schema::table('stock_histories', function (Blueprint $table) {
      $table->decimal('quantity', 10, 2)->change();
      $table->decimal('stock_before', 10, 2)->change();
      $table->decimal('stock_after', 10, 2)->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('stock_histories', function (Blueprint $table) {
      $table->smallInteger('quantity')->change();
      $table->smallInteger('stock_before')->change();
      $table->smallInteger('stock_after')->change();
    });
  }
};
