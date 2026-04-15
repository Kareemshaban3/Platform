<?php

namespace App\Http\Requests\Attachments;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'main_image' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,pdf|max:51200',

        ];
    }
}
