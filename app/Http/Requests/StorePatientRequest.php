<?php

namespace App\Http\Requests;

use App\Rules\CNS;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document' => 'required|cpf|unique:patients',
            'cns' => ['required', 'unique:patients', new CNS()],
            'name' => 'required',
            'mother_name' => 'required',
            'birthdate' => 'required|date_format:Y-m-d',
            'phone' => 'required|celular_com_ddd',
            'address' => 'nullable|array',
            'address.*.zipcode' => 'required',
            'address.*.city' => 'required',
            'address.*.state' => 'required',
            'address.*.neighborhood' => 'required',
            'address.*.street' => 'required',
            'address.*.number' => 'required',
            'address.*.complement' => 'nullable',
            'photo' => 'required|image',
        ];
    }
}
