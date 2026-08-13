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
            'originSkyId' => 'required|string',
            'destinationSkyId' => 'required|string',
            'originEntityId' => 'required|string',
            'destinationEntityId' => 'required|string',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'cabinClass' => 'nullable|in:economy,premium_economy,business,first',
            'adults' => 'nullable|integer|min:1|max:9',
            'sortBy' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
        ];
    }
}
