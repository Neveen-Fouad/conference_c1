<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'logo' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Please upload a logo.',
            'logo.image' => 'The file must be an image.',
            'logo.mimes' => 'The logo must be a JPG, JPEG, PNG, or SVG file.',
            'logo.max' => 'The logo size must not exceed 2 MB.',
        ];
    }
}
