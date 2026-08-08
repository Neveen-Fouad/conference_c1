<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HotelSearchRequest extends FormRequest
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

            'destination' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'check_in' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'check_out' => [
                'required',
                'date',
                'after:check_in',
            ],

            'guests' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            'budget' => [
                'required',
                'numeric',
                'min:1',
            ],

            'sort_by' => [
                'nullable',
                'in:review,price_low,price_high',
            ],

        ];
    }
}
