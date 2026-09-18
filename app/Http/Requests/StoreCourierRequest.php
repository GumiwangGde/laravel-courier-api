<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:couriers,phone'],
            'email' => ['nullable', 'email', 'max:100', 'unique:couriers,email'],
            'level' => ['required', 'integer', 'between:1,5'],
            'is_active' => ['nullable', 'boolean'],
            'registered_at' => ['nullable', 'date'],
        ];
    }
}
