<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classes' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'number_of_travels' => 'required|integer|min:1',
            'estimated_expenses' => 'required|numeric|min:0',
            'budget' => 'required|numeric|min:0',
            'number_of_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'style' => 'required|string|max:255',

        ];
    }

    public function messages(): array
    {
        return [
            'classes.required' => 'Class is required.',
            'destination.required' => 'Destination is required.',
            'number_of_travels.required' => 'Number of travels is required.',
            'estimated_expenses.required' => 'Estimated expenses are required.',
            'budget.required' => 'Budget is required.',
            'number_of_days.required' => 'Number of days is required.',
            'start_date.required' => 'Start date is required.',
            'style.required' => 'Travel style is required.',
        ];
    }
}
