<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'long' => $this->input('long', $this->input('longitude')),
            'latittude' => $this->input('latittude', $this->input('latitude')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'long' => 'required|numeric|between:-180,180',
            'latittude' => 'required|numeric|between:-90,90',
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            ],
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {

        return [
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
        ];
    }
}
