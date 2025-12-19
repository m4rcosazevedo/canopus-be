<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['id' => 11, 'abbr' => 'RO', 'name' => 'Rondônia', 'ibge_code' => 11],
            ['id' => 12, 'abbr' => 'AC', 'name' => 'Acre', 'ibge_code' => 12],
            ['id' => 13, 'abbr' => 'AM', 'name' => 'Amazonas', 'ibge_code' => 13],
            ['id' => 14, 'abbr' => 'RR', 'name' => 'Roraima', 'ibge_code' => 14],
            ['id' => 15, 'abbr' => 'PA', 'name' => 'Pará', 'ibge_code' => 15],
            ['id' => 16, 'abbr' => 'AP', 'name' => 'Amapá', 'ibge_code' => 16],
            ['id' => 17, 'abbr' => 'TO', 'name' => 'Tocantins', 'ibge_code' => 17],
            ['id' => 21, 'abbr' => 'MA', 'name' => 'Maranhão', 'ibge_code' => 21],
            ['id' => 22, 'abbr' => 'PI', 'name' => 'Piauí', 'ibge_code' => 22],
            ['id' => 23, 'abbr' => 'CE', 'name' => 'Ceará', 'ibge_code' => 23],
            ['id' => 24, 'abbr' => 'RN', 'name' => 'Rio Grande do Norte', 'ibge_code' => 24],
            ['id' => 25, 'abbr' => 'PB', 'name' => 'Paraíba', 'ibge_code' => 25],
            ['id' => 26, 'abbr' => 'PE', 'name' => 'Pernambuco', 'ibge_code' => 26],
            ['id' => 27, 'abbr' => 'AL', 'name' => 'Alagoas', 'ibge_code' => 27],
            ['id' => 28, 'abbr' => 'SE', 'name' => 'Sergipe', 'ibge_code' => 28],
            ['id' => 29, 'abbr' => 'BA', 'name' => 'Bahia', 'ibge_code' => 29],
            ['id' => 31, 'abbr' => 'MG', 'name' => 'Minas Gerais', 'ibge_code' => 31],
            ['id' => 32, 'abbr' => 'ES', 'name' => 'Espírito Santo', 'ibge_code' => 32],
            ['id' => 33, 'abbr' => 'RJ', 'name' => 'Rio de Janeiro', 'ibge_code' => 33],
            ['id' => 35, 'abbr' => 'SP', 'name' => 'São Paulo', 'ibge_code' => 35],
            ['id' => 41, 'abbr' => 'PR', 'name' => 'Paraná', 'ibge_code' => 41],
            ['id' => 42, 'abbr' => 'SC', 'name' => 'Santa Catarina', 'ibge_code' => 42],
            ['id' => 43, 'abbr' => 'RS', 'name' => 'Rio Grande do Sul', 'ibge_code' => 43],
            ['id' => 50, 'abbr' => 'MS', 'name' => 'Mato Grosso do Sul', 'ibge_code' => 50],
            ['id' => 51, 'abbr' => 'MT', 'name' => 'Mato Grosso', 'ibge_code' => 51],
            ['id' => 52, 'abbr' => 'GO', 'name' => 'Goiás', 'ibge_code' => 52],
            ['id' => 53, 'abbr' => 'DF', 'name' => 'Distrito Federal', 'ibge_code' => 53],
        ];

        DB::table('states')->insert($states);
    }
}
