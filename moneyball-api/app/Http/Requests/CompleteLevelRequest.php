<?php

namespace App\Http\Requests;

/**
 * Class CompleteLevelRequest
 * @package App\Http\Requests
 */
class CompleteLevelRequest extends ApiRequest
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
            'chip' => 'required|numeric',
            'cash' => 'required|numeric',
            'lives' => 'required|numeric',
        ];
    }
}