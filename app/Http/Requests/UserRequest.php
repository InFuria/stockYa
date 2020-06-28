<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'dni' => 'required|numeric',
            'company_id' => 'numeric',
            'name' => 'required|string',
            'username' => 'required|string',
            'address' => 'string',
            'phone' => 'string',
            'status' => 'boolean',
            'email' => 'required',
            'password' => 'required|confirmed',
            'roles' => 'array'
        ];
    }
}
