<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DancerRequest extends FormRequest
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
            'role' => 'required',
            'user_id' => 'required|integer|exists:app_users,id',
            'age_group_id' => 'required_unless:role,leader|integer|exists:age_groups,id',
            'dance_group_id' => 'required|integer|exists:dance_groups,id',
        ];
    }
}
