<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ToursListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priceFrom' => 'numeric',
            'priceTo' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' =>Rule::in(['price']),
            'orderBy' => Rule::in(['asc', 'desc'])
        ];
    }
    public function messages(): array
    {
        return [
            'sortBy' => "the 'sortBy' parameter accept only 'price' value",
            'orderBy' => "the 'orderBy' parameter accept only 'asc' or 'desc' value"
        ];
    }
}
