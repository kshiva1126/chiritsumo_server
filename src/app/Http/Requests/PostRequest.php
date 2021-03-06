<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:50',
            'content' => 'required|string|max:1000',
        ];
    }

    /**
     * [Override] バリデーション失敗時
     *
     * @param Validator $validator
     *
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response['data'] = [];
        $response['status'] = 'NG';
        $response['summary'] = 'Failed Validation.';
        $response['errors'] = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json($response, 422)
        );
    }
}
