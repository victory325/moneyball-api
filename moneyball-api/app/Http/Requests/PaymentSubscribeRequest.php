<?php

namespace App\Http\Requests;

/**
 * Class PaymentSubscribeRequest
 * @package App\Http\Requests
 */
class PaymentSubscribeRequest extends ApiRequest
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
            'authorization_code' => 'required|string',
            'refresh_token' => 'string'
        ];
    }
}