<?php

namespace App\Http\Requests\Traits;

trait HasListNewsValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'required|numeric|min:1',
            'results' => 'required|numeric|min:10',
            'start_date' => 'sometimes|string|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'sometimes|string|date_format:Y-m-d|after_or_equal:start_date',
            'timezone' => 'required_with:start_date,end_date|string',
            'category' => 'sometimes|string|max:10',
            'keyword' => 'sometimes|string|max:100|nullable',
        ];
    }
}
