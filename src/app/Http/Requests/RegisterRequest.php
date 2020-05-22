<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'description' => [ 'max:255'],
            'image'  => ['image', 'max:3000'],
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
