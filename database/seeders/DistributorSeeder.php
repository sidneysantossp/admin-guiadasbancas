<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vendors')->updateOrInsert(
            ['email' => 'distribuidor@admin.com'],
            [
                'f_name' => 'Distribuidor',
                'l_name' => 'Master',
                'phone' => '11999999999',
                'email' => 'distribuidor@admin.com',
                'password' => Hash::make('12345678'),
                'status' => 1,
                'distributor' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
