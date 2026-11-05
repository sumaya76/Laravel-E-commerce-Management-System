<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => [
                'required',
                'integer',
                Rule::exists('subcategories', 'id')
                    ->where('category_id', $this->input('category_id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'old_price' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'new_price' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
                'lte:old_price',
                'decimal:0,2',
            ],
        ];
    }
}
