<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchHotelRequest extends FormRequest
{
   
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'country_name' => ['required', 'string'],
            'city' => ['nullable', 'string'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests' => ['required', 'integer', 'min:1'],
        ];
    }

    
}