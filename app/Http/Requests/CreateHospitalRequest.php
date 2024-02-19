<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHospitalRequest extends FormRequest
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
        return [
            'clientname' => 'required|string',
            'address' => 'required|string',
            'orgid_prod' => 'required|string',
            'clientid_prod' => 'required|string',
            'clientsecret_prod' => 'required|string',
        ];
    }
}
