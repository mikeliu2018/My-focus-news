<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\HasListNewsValidation;

class ListNewsFocusRequest extends FormRequest
{
    use HasListNewsValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 假設所有通過 API 認證的使用者都可以執行此操作
        return true;
    }
}
