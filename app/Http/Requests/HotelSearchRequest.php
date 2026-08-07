<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelSearchRequest extends FormRequest
{
    public function rules(): array
{
    return [
        'region_id' => 'required|integer',
        'checkin_date' => 'required|date',
        'checkout_date' => 'required|date',
        'adults_number' => 'required|integer|min:1',
        'domain' => 'required|string',
        'locale' => 'required|string',
        'page_number' => 'nullable|integer|min:1',
        'sort_order' => 'required|string',
    ];
}
} 