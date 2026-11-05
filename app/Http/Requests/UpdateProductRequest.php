<?php

namespace App\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['remove_image'] = ['sometimes', 'boolean'];

        return $rules;
    }
}
