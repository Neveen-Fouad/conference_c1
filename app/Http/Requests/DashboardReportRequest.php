<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardReportRequest extends FormRequest
{
    /**
     * Only admins should reach this request at all — the route itself is
     * also protected by the isAdmin middleware, but this is a second
     * layer of defense in case the request class is reused elsewhere.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Both fields are optional — when omitted, the controller/service
     * defaults to the current month/year. When provided, they let an
     * admin pull stats for a specific past period.
     */
    public function rules(): array
    {
        return [
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'digits:4', 'between:2000,'.now()->year],
        ];
    }

    public function messages(): array
    {
        return [
            'month.between' => 'Month must be between 1 and 12.',
            'year.between' => 'Year must be a valid year between 2000 and the current year.',
        ];
    }
}
