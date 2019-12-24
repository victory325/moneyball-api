<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Class DefaultListRequest
 *
 * @package App\Http\Requests
 */
class DefaultListRequest extends ApiRequest
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
            'order_by'      => 'string|nullable',
            'order'         => ['string', Rule::in(['asc', 'desc'])],
            'per_page'      => 'integer|nullable',
            'page'          => 'integer|nullable',
            'filter.column' => 'string|nullable',
            'filter.value'  => 'string|nullable',
        ];
    }
}