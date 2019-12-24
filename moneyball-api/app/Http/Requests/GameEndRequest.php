<?php

namespace App\Http\Requests;

/**
 * Class GameEndRequest
 * @package App\Http\Requests
 */
class GameEndRequest extends ApiRequest
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
            'winner_id'         => 'required|numeric',
            'prize'             => 'required|numeric',
            'game_name'         => 'string',
        ];
    }
}