<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_city' => 'required|string|max:100',
            'origin_country' => 'nullable|string|max:100',
            'destination_city' => 'required|string|max:100',
            'destination_country' => 'nullable|string|max:100',
            'departure_date' => 'required|date_format:Y-m-d',
            'return_date' => 'nullable|date_format:Y-m-d|after_or_equal:departure_date',
            'travelers' => 'nullable|integer|min:1|max:9',
            'cabin_class' => 'nullable|in:economy,premium_economy,business,first',
            'sort_by' => 'nullable|string',
        ];
    }
}