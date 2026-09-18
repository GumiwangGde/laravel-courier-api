<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courier = $this->route('courier');

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('couriers', 'phone')->ignore($courier),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('couriers', 'email')->ignore($courier),
            ],
            'level' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'is_active' => ['sometimes', 'boolean'],
            'registered_at' => ['nullable', 'date'],
        ];
    }
}
