<?php

namespace App\Http\Requests;

/**
 * Class ConnectOnesignalRequest
 * @package App\Http\Requests
 */
class ConnectOnesignalRequest extends ApiRequest
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
            'onesignal_id' => 'required|string',
        ];
    }
}