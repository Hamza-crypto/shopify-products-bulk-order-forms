<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerSubmitRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.selected' => 'sometimes|accepted',
            'products.*.title' => 'required|string',
            'products.*.price' => 'required|numeric',
            'products.*.sku' => 'required|string',
        ];
    }
}