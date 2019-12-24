<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ApiRequest
 *
 * @package App\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw ValidationException::withMessages($validator->errors()->toArray());
    }
}