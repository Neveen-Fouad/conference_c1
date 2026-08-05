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
            'reviewable_id' => ['required','string'],
            'type' => ['required',
            new Enum(ReviewType::class)],
            'rating' => ['required','decimal:2,1'],
            'description' => ['required','string'],
            'image' =>['required','text'],

        ];
    }
}
