<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|max:255|unique:users,email,' . auth()->id(),
            'phone'      => 'sometimes|string|max:20',
            'birth_date' => 'sometimes|date',
            'latittude'  => 'sometimes|numeric',
            'long'       => 'sometimes|numeric',
        ];
    }
}
