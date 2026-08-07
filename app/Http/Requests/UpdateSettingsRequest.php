<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           

        'logo' => 'sometimes|image|mimes:jpg,jpeg,png,svg|max:2048',
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'slogan' => 'sometimes|string|max:255',
            'facebook' => 'sometimes|url|max:255',
            'instagram' => 'sometimes|url|max:255',

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
