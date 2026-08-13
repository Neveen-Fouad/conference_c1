<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'long' => 'required|string|max:255',
            'latittude' => 'required|string|max:255',
            'birth_date' => [
                    'required',
                    'date_format:d/m/Y',
                    'before_or_equal:' . now()->subYears(18)->format('d/m/Y'),
            ],
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array{

        return[
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
        ];
    }
}
