<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NAWebSaleRequest extends FormRequest
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
            'client_name' => 'required|string',
            'email' => 'string',
            'phone' => 'string',
            'address' => 'required|string',
            'delivery' => 'boolean',
            'company_id' => 'required|numeric',
            'tags' => 'string',
            'text' => 'string'
        ];
    }
}
