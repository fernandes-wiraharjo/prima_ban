<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Hash;
use DB;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $now = Carbon::now();

    $users = [
      [
        'username' => 'admin',
        'password' => Hash::make('12345'),
        'created_by' => null,
        'created_at' => $now,
        'updated_by' => null,
        'updated_at' => null,
      ],
    ];

    foreach ($users as $user) {
      $user_has_exist = DB::table('users')
        ->where('username', '=', $user['name'])
        ->exists();

      if (!$user_has_exist) {
        DB::table('users')->insert($user);
      }
    }
  }
}
