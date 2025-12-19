<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Busca os municípios na API
        $response = Http::get('https://servicodados.ibge.gov.br/api/v1/localidades/municipios');

        if ($response->successful()) {
            $cities = $response->json();
            $data = [];

            foreach ($cities as $city) {
                // O segredo: Pegamos os 2 primeiros dígitos do ID da cidade
                // para saber o ID do estado (ex: 3550308 -> 35 que é SP)
                $stateId = substr($city['id'], 0, 2);

                $data[] = [
                    'state_id'   => (int) $stateId,
                    'name'       => $city['nome'],
                    'ibge_code'  => $city['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($data) >= 500) {
                    DB::table('cities')->insert($data);
                    $data = [];
                }
            }

            if (!empty($data)) {
                DB::table('cities')->insert($data);
            }
        }
    }
}
