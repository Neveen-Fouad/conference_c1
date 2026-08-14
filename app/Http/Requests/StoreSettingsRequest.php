<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSettingsRequest extends FormRequest
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
            //

            'logo' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'slogan' => 'required|string|max:255',
            'facebook' => 'required|url|max:255',
            'instagram' => 'required|url|max:255',

        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Please upload a logo.',
            'logo.image' => 'The file must be an image.',
            'logo.mimes' => 'The logo must be a JPG, JPEG, PNG, or SVG file.',
            'logo.max' => 'The logo size must not exceed 2 MB.',

            'name.required' => 'Site name is required.',
            'name.string' => 'Site name must be a string.',
            'name.max' => 'Site name cannot exceed 255 characters.',

            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',

            'slogan.required' => 'Slogan is required.',
            'slogan.string' => 'Slogan must be a string.',
            'slogan.max' => 'Slogan cannot exceed 255 characters.',

            'facebook.required' => 'Facebook link is required.',
            'facebook.url' => 'Please enter a valid Facebook URL.',

            'instagram.required' => 'Instagram link is required.',
            'instagram.url' => 'Please enter a valid Instagram URL.',

        ];
    }
}
