<?php

namespace App\Http\Requests;

/**
 * Class LoginUserRequest
 *
 * @package App\Http\Requests
 */
class LoginUserRequest extends ApiRequest
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
            'email'        => 'required_without_all:phone,user_id,instagram_id,facebook_id|string|email|max:255',
            'user_id'      => 'required_without_all:email,phone,instagram_id,facebook_id|string',
            'phone'        => 'required_without_all:email,user_id,instagram_id,facebook_id|string|min:11',
            'password'     => 'required_without_all:instagram_id,facebook_id|string',
            'instagram_id' => 'required_without_all:email,phone,user_id,facebook_id|numeric',
            'facebook_id'  => 'required_without_all:email,phone,user_id,instagram_id|numeric',
            'access_token' => 'required_with:instagram_id,facebook_id|string',
        ];
    }
}