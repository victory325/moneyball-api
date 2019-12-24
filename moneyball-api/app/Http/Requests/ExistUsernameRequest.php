<?php

namespace App\Http\Requests;

/**
 * Class ExistUsernameRequest
 *
 * @package App\Http\Requests
 */
class ExistUsernameRequest extends ApiRequest
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
            'user_id'   => 'required:string',
        ];
    }
}