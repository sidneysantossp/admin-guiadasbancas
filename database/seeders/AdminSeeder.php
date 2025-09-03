<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'f_name' => 'Master Admin',
                'l_name' => 'Khandakar',
                'phone' => '01759412381',
                'email' => 'admin@admin.com',
                'image' => 'def.png',
                'password' => bcrypt('12345678'),
                'role_id' => 1,
                'remember_token' =>Str::random(10),
                'updated_at'=>now()
            ]
        );
    }
}
