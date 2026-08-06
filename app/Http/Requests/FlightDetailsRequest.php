<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'itineraryId' => 'required|string',
            'sessionId' => 'required|string',
            'legs' => 'required',
            'adults' => 'nullable|integer|min:1',
            'currency' => 'nullable|string',
        ];
    }
}