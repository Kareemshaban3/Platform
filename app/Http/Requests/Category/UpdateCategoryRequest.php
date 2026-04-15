<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->route('category')->id),
            ],
            'type'         => ['sometimes', 'nullable', 'string', Rule::in(['middle', 'advance', 'beginner'])],
            'description'  => ['sometimes', 'nullable', 'string', 'max:1000'],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'time'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'lesson_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'image'        => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'الحقل type يجب أن يكون واحد من القيم التالية: middle, advance, beginner.',
        ];
    }
}
