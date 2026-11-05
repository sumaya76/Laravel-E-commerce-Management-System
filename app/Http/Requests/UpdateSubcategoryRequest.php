<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateSubcategoryRequest extends StoreSubcategoryRequest
{
    public function rules(): array
    {
        $subcategory = $this->route('subcategory');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')
                    ->where('category_id', $this->input('category_id'))
                    ->ignore($subcategory),
            ],
        ];
    }
}
