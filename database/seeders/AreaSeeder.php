<?php

namespace Database\Seeders;

use App\Modules\Area\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            'Linguagens',
            'Humanas',
            'Natureza',
            'Matemática',
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(['name' => $area]);
        }
    }
}
