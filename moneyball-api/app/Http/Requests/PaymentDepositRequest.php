<?php

namespace App\Http\Requests;

/**
 * Class PaymentDepositRequest
 * @package App\Http\Requests
 */
class PaymentDepositRequest extends ApiRequest
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
            'authorization_code'=> 'required|string',
            'amount'            => 'required|numeric',
            'summary'           => 'required|string',
            'bonus'             => 'numeric'
        ];
    }
}