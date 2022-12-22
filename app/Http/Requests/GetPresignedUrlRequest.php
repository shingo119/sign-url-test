<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPresignedUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
        'filename' => 'bail|string|required',     // 文字列、必須ルール
        // 'method' => 'string|required|in:get,put' // 'get' or 'put' の文字列、必須ルール
    ];
    }
}
