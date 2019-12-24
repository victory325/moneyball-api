<?php

namespace App\Http\Requests;

/**
 * Class PasswordChangeRequest
 *
 * @package App\Http\Requests
 */
class PasswordChangeRequest extends ApiRequest
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
            'cur_password'      => 'string|nullable',
            'new_password'      => 'required',
            'email'             => 'string',
            'token'             => 'string'
        ];
    }
}