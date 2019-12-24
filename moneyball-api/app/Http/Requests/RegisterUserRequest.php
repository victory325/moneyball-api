<?php

namespace App\Http\Requests;

/**
 * Class RegisterUserRequest
 *
 * @package App\Http\Requests
 */
class RegisterUserRequest extends ApiRequest
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
//        print_r('dfdfd');exit;
        return [
            'name'     => 'string|max:255',
            'phone'    => 'required_without:email|string|min:11|unique:users,phone',
            'email'    => 'required_without:phone|string|email|max:255|unique:users,email',
            'user_id'  => 'string|unique:users,user_id',
            'password' => 'required|string|min:6',
        ];
    }
}
