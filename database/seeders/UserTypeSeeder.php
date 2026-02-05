<?php

namespace Database\Seeders;

use App\Modules\UserType\Enums\UserTypeEnum;
use App\Modules\UserType\Enums\UserTypeIdEnum;
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
                'id' => UserTypeIdEnum::ROOT,
                'name' => UserTypeEnum::ROOT,
                'description' => UserTypeEnum::ROOT,
                'created_at' => $date,
                'updated_at' => $date,
                'visible' => false
            ],
            [
                'id' => UserTypeIdEnum::ADMINISTRATOR,
                'name' => UserTypeEnum::ADMINISTRATOR,
                'description' => UserTypeEnum::ADMINISTRATOR,
                'created_at' => $date,
                'updated_at' => $date,
                'visible' => false
            ],
            [
                'id' => UserTypeIdEnum::INSTRUCTOR,
                'name' => UserTypeEnum::INSTRUCTOR,
                'description' => UserTypeEnum::INSTRUCTOR,
                'created_at' => $date,
                'updated_at' => $date,
                'visible' => true
            ],
            [
                'id' => UserTypeIdEnum::STUDENT,
                'name' => UserTypeEnum::STUDENT,
                'description' => UserTypeEnum::STUDENT,
                'created_at' => $date,
                'updated_at' => $date,
                'visible' => true
            ],
        ]);
    }
}
