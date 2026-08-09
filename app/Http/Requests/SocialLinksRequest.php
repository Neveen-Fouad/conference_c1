<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facebook' => 'required|url|max:255',
            'instagram' => 'required|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'facebook.required' => 'Facebook link is required.',
            'facebook.url' => 'Please enter a valid Facebook URL.',

            'instagram.required' => 'Instagram link is required.',
            'instagram.url' => 'Please enter a valid Instagram URL.',
        ];
    }
}