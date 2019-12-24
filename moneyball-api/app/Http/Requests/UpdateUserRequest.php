<?php

namespace App\Http\Requests;

/**
 * Class UpdateUserRequest
 * @package App\Http\Requests
 */
class UpdateUserRequest extends ApiRequest
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
            'name'             => 'sometimes|required|string|max:255',
            'phone'            => 'sometimes|required|string|min:11',
            'email'            => 'sometimes|required|string|email|max:255',
            'level'            => 'sometimes|required|numeric',
            'lives'            => 'sometimes|required|numeric',
            'is_seconds_added' => 'nullable|boolean',
            'avatar'           => 'nullable|string',
        ];
    }
}