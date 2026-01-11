<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZipCodeService
{
    // Lista de provedores em ordem de prioridade
    protected array $providers = [
        'brasilapi' => 'https://brasilapi.com.br/api/cep/v1/',
        'viacep'    => 'https://viacep.com.br/ws/',
    ];

    // Lista comum de tipos de logradouro para o parser.
    public array $streetTypes = [
        'Rua', 'Avenida', 'Av.', 'Praça', 'Alameda', 'Travessa', 'Rodovia',
        'Viela', 'Ladeira', 'Estrada', 'Bloco', 'Conjunto', 'Setor'
    ];

    public function findAddress(string $zipCode): ?array
    {
        $zipCode = preg_replace('/[^0-9]/', '', $zipCode);
        $cacheKey = "zipcode_address_{$zipCode}";

        return Cache::remember($cacheKey, now()->addYear(), function () use ($zipCode) {
            foreach ($this->providers as $name => $url) {
                try {
                    $response = $this->fetchFromProvider($name, $url, $zipCode);

                    if ($response) {
                        return $response;
                    }
                } catch (\Exception $e) {
                    Log::warning("Provider {$name} falhou para o CEP {$zipCode}");
                    continue;
                }
            }

            return null;
        });
    }

    private function fetchFromProvider(string $name, string $url, string $zipCode): ?array
    {
        if ($name === 'brasilapi') {
            $res = Http::get($url . $zipCode);
            if ($res->successful()) {
                $data = $res->json();

                $rawStreet = $data['street'] ?? '';
                $parsed = $this->parseStreet($rawStreet);

                return [
                    'zip_code' => preg_replace('/[^0-9]/', '', $data['cep'] ?? $zipCode),
                    'street_type' => $parsed['type'],
                    'street_name' => $parsed['name'],
                    'district' => $data['neighborhood'] ?? '',
                    'city'     => $data['city'] ?? '',
                    'state'    => $data['state'] ?? '',
                ];
            }
        }

        if ($name === 'viacep') {
            $res = Http::get($url . "{$zipCode}/json/");
            if ($res->successful() && !isset($res->json()['erro'])) {
                $data = $res->json();

                $rawStreet = $data['logradouro'] ?? '';
                $parsed = $this->parseStreet($rawStreet);

                return [
                    'zip_code' => preg_replace('/[^0-9]/', '', $data['cep'] ?? $zipCode),
                    'street_type' => $parsed['type'],
                    'street_name' => $parsed['name'],
                    'district' => $data['bairro'] ?? '',
                    'city'     => $data['localidade'] ?? '',
                    'state'    => $data['uf'] ?? '',
                ];
            }
        }

        return null;
    }

    /**
     * Separa o "Tipo" do "Nome" do logradouro.
     * Ex: "Avenida Paulista" -> type: "Avenida", name: "Paulista"
     */
    private function parseStreet(string $fullStreet): array
    {
        if (empty($fullStreet)) {
            return ['type' => null, 'name' => ''];
        }

        $parts = explode(' ', $fullStreet, 2);
        $firstWord = $parts[0];

        foreach ($this->streetTypes as $type) {
            if (Str::lower($firstWord) === Str::lower($type)) {
                return [
                    'type' => $type,
                    'name' => $parts[1] ?? ''
                ];
            }
        }

        return [
            'type' => 'Rua',
            'name' => $fullStreet
        ];
    }
}
