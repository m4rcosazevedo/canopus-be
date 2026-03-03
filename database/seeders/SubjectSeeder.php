<?php

namespace Database\Seeders;

use App\Modules\Area\Models\Area;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Linguagens' => [
                ['name' => 'Língua Portuguesa', 'color' => '#E74C3C'],
                ['name' => 'Literatura', 'color' => '#C0392B'],
                ['name' => 'Língua Inglesa', 'color' => '#9B59B6'],
                ['name' => 'Artes', 'color' => '#E67E22'],
                ['name' => 'Educação Física', 'color' => '#16A085'],
            ],
            'Humanas' => [
                ['name' => 'História', 'color' => '#8E44AD'],
                ['name' => 'Geografia', 'color' => '#27AE60'],
                ['name' => 'Filosofia', 'color' => '#2C3E50'],
                ['name' => 'Sociologia', 'color' => '#7F8C8D'],
            ],
            'Natureza' => [
                ['name' => 'Biologia', 'color' => '#2ECC71'],
                ['name' => 'Química', 'color' => '#F1C40F'],
                ['name' => 'Física', 'color' => '#3498DB'],
            ],
            'Matemática' => [
                ['name' => 'Matemática', 'color' => '#1ABC9C'],
            ],
        ];

        foreach ($subjects as $areaName => $areaSubjects) {
            $area = Area::where('name', $areaName)->first();

            if (!$area) continue;

            foreach ($areaSubjects as $subject) {
                Subject::query()->firstOrCreate([
                    'name' => $subject['name'],
                    'area_id' => $area->id,
                ], [
                    'color_hex' => $subject['color'],
                    'slug' => Str::slug($subject['name']),
                ]);
            }
        }
    }
}
