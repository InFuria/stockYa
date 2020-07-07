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
            'company_id' => 'required|numeric',
            'delivery' => 'boolean',
            'tags' => 'string',
            'text' => 'string'
        ];
    }
}
