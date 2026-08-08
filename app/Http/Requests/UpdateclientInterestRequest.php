<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateclientInterestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'interests' => 'required|array|min:1|max:4',
            'interests.*' => 'exists:interests,id|distinct',
        ];
    }
}
