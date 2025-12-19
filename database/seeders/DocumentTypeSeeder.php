<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = now();
        DB::table('document_types')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'CPF',
                'description' => 'Cadastro de Pessoa Física',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 2,
                'name' => 'RG',
                'description' => 'Registro Geral',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 3,
                'name' => 'CNPJ',
                'description' => 'Cadastro Nacional de Pessoa Jurídica',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 4,
                'name' => 'CNH',
                'description' => 'Carteira Nacional de Habilitação',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 5,
                'name' => 'Passaporte',
                'description' => '',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ]);
    }
}
