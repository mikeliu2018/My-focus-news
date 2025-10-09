<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HasListNewsValidation;

class ListNewsRequest extends FormRequest
{
    use HasListNewsValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 此 API 為公開，任何人都可以請求
        return true;
    }
}
