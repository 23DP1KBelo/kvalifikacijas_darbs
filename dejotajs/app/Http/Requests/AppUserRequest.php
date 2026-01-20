<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'email' => 'required|email|unique:app_users,email',
            'password' => 'required|min:8|max:50',
            'phone_number' => 'required|max:20',
        ];
    }
}
