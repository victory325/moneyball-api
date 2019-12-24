<?php

namespace App\Http\Requests;

/**
 * Class ValidateRegisterUserRequest
 *
 * @package App\Http\Requests
 */
class ValidateRegisterUserRequest extends ApiRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'phone'   => 'required_without_all:email|numeric|min:11',
            'email'   => 'required_without_all:phone|string|max:255',
            'token'   => 'required|min:4|numeric',
        ];
    }
}