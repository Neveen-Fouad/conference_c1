<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
            'destination' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'number_of_days' => 'sometimes|required|integer|min:1',
            'estimated_expenses' => 'sometimes|required|numeric|min:0',
        ];
    }
}
