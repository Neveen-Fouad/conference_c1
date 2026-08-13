<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:20',
            'slogan' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',

            'slogan.required' => 'Slogan is required.',
            'slogan.string' => 'Slogan must be a string.',
            'slogan.max' => 'Slogan cannot exceed 255 characters.',
        ];
    }
}
