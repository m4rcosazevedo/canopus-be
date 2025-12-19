<?php

namespace App\Http\Requests\Address;

use App\Models\Address;
use App\Services\ZipCodeService;

class AddressWithAutoCompleteRequest extends AddressRequest
{
    public function rules(): array
    {
        return [
            'zip_code' => 'required|string|size:8'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) return;

            $zipService = app(ZipCodeService::class);
            $externalData = $zipService->findAddress($this->zip_code);

            if (!$externalData) {
                $validator->errors()->add('zip_code', 'CEP inválido ou não encontrado.');
                return;
            }

            $exists = Address::where('zip_code', $externalData['zip_code'])
                ->where('street_type', $externalData['street_type'])
                ->where('street_name', $externalData['street_name'])
                ->exists();

            if ($exists) {
                $validator->errors()->add('zip_code', 'Este logradouro já está cadastrado para este CEP.');
            }

            $this->merge(['auto_complete_data' => $externalData]);
        });
    }
}
