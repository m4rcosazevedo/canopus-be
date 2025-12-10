<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = now();
        DB::table('user_types')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'administrator',
                'description' => '',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 2,
                'name' => 'instructor',
                'description' => '',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 3,
                'name' => 'student',
                'description' => '',
                'created_at' => $date,
                'updated_at' => $date
            ],
        ]);
    }
}
