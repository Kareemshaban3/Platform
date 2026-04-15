<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255', 'unique:categories,name'],
            'type'         => ['required', 'string', 'in:middle,advance,beginner'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'time'         => ['required', 'integer', 'min:1'],
            'lesson_count' => ['required', 'integer', 'min:1'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'الحقل type يجب أن يكون واحد من القيم التالية: middle, advance, beginner.',
        ];
    }
}
