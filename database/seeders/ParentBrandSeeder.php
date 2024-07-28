<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Hash;
use DB;

class ParentBrandSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $now = Carbon::now();

    $parentBrands = [
      [
        'name' => 'Bridgestone',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'GT',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Dunlop',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Goodyear',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Toyo',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Michelin',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Hankook',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Maxxis',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Accelera',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Pirelli',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Continental',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Pertamina',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Shell',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Jumbo',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Mobil',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Castrol',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Deli',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Isuzu',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Sakura',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Toyota',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Arpi',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Astra',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Mitsubishi',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'GS',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
      [
        'name' => 'Yuasa',
        'is_active' => true,
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
    ];

    foreach ($parentBrands as $pb) {
      $data_has_exist = DB::table('parent_brands')
        ->where('name', '=', $pb['name'])
        ->exists();

      if (!$data_has_exist) {
        DB::table('parent_brands')->insert($pb);
      }
    }
  }
}
