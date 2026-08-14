<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
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
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'budget' => 'required|numeric|min:100',
            'number_of_travels' => 'required|integer|min:1',
            'number_of_days' => 'required|integer|min:1',
            'estimated_expenses' => 'nullable|numeric|min:0',
            'style' => 'required|string|max:255',
            'classes' => 'sometimes|string|max:255',
            'preferences' => 'nullable|string',
            'details' => 'nullable|array',
            'details.*.day' => 'required_with:details|integer|min:1',
            'details.*.title' => 'required_with:details|string|max:255',
            'details.*.expenses' => 'nullable|numeric|min:0',
            'details.*.plan' => 'required_with:details',
        ];
    }
}
