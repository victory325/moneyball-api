<?php

namespace App\Http\Requests;

/**
 * Class SendTokenRequest
 *
 * @package App\Http\Requests
 */
class SendTokenRequest extends ApiRequest
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
            'email'      => 'required|string'
        ];
    }
}