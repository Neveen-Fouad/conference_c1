<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookFlightRequest extends FormRequest
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
            'itinerary' => ['required', 'array'],

            'itinerary.id' => ['required', 'string'],

            'itinerary.price' => ['required', 'array'],
            'itinerary.price.amount' => ['required', 'numeric', 'min:0'],

            'itinerary.departure' => ['required', 'date', 'after_or_equal:today'],
            'itinerary.arrival' => ['required', 'date', 'after:itinerary.departure'],

            'itinerary.legs' => ['required', 'array', 'min:1'],
            'itinerary.legs.0.carriers' => ['required', 'array', 'min:1'],
            'itinerary.legs.0.carriers.0.name' => ['required', 'string'],

            'cabin_class' => [
                'required',
                'in:economy,premium_economy,business,first',
            ],

            'adults' => [
                'required',
                'integer',
                'min:1',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
            ],
        ];
    }
}
