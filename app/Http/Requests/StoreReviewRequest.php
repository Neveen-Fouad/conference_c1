<?php

namespace App\Http\Requests;

use App\Enum\ReviewType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreReviewRequest extends FormRequest
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
            'reviewable_id' => ['required', 'string', 'max:500'],
            'type' => ['required',
                new Enum(ReviewType::class)],
            'rating' => ['required', 'numeric', 'between:0,5'],
            'description' => ['required', 'string'],
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',

        ];
    }
}
