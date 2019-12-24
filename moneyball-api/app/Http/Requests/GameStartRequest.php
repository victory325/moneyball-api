<?php

namespace App\Http\Requests;

/**
 * Class GameStartRequest
 * @package App\Http\Requests
 */
class GameStartRequest extends ApiRequest
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
            'players'           => 'array',
            'players.*'         => 'numeric',
            'game_name'         => 'string',
            'entry_cash'        => 'numeric',
            'entry_chip'        => 'numeric',
        ];
    }
}