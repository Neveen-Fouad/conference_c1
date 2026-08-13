<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banner' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'banner.required' => 'Banner image is required.',
            'banner.image' => 'The banner must be an image.',
            'banner.mimes' => 'The banner must be a JPG, JPEG, PNG, or SVG file.',
            'banner.max' => 'The banner size must not exceed 2 MB.',
        ];
    }
}
