<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name' => 'required|string',
            'address' => 'string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'whatsapp' => 'string',
            'social' => 'string',
            'city_id' => 'integer',//temporal -> required
            'image' => 'array',
            'score' => 'integer',
            'delivery' => 'integer',
            'zone' => 'string',
            'status' => 'integer',
            'attention_hours' => 'string',
            'category_id' => 'required|integer',
            'company_id' => 'integer',
            'visits' => 'integer'
        ];
    }
}
