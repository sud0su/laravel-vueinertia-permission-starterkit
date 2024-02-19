<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCronRequest extends FormRequest
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
            'rsklien_id' => 'required|array',
            'crontitle' => 'required|string',
            'endpoint' => 'required|string',
            'croncat' => 'string',
            'day' => 'string',
            'hour' => 'string',
            'minute' => 'string',
        ];
    }
}
