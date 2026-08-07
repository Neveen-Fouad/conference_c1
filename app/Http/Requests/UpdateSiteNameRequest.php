<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Site name is required.',
            'name.string' => 'Site name must be a string.',
            'name.max' => 'Site name cannot exceed 255 characters.',
            
        ];
    }
}
