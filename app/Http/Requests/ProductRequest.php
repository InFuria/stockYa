<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'type' => 'string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'company_id' => 'required|integer',
            'status' => 'integer',
            'visits' => 'integer',
            'image' => 'array'
        ];
    }
}
