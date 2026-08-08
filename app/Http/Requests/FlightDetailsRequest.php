<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FlightDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'itineraryId'         => 'required|string',
            'sessionId'           => 'required|string',
            'legs'                => 'required|array|min:1',
            'legs.*.origin'       => 'required|string',
            'legs.*.destination'  => 'required|string',
            'legs.*.date'         => 'required|date_format:Y-m-d',
            'currency'            => 'nullable|string|size:3',
            'cabinClass'          => 'nullable|in:economy,premium_economy,business,first',
            'adults'              => 'nullable|integer|min:1|max:9',
        ];
    }
}
