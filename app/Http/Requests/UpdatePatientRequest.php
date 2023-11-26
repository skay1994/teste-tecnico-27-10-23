<?php

namespace App\Http\Requests;

use App\Rules\CNS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('patient')->getKey();

        return [
            'document' => ['required', 'cpf', Rule::unique('patients')->ignore($id)] ,
            'cns' => ['required', 'unique:patients', new CNS(), Rule::unique('patients')->ignore($id)],
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
