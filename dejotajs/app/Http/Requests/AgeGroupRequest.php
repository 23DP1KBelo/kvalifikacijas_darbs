<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgeGroupRequest extends FormRequest
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
            'name' => 'required|max:255',
            'age_group' => 'required|max:255',
            'dance_group_id' => 'required|integer|exists:dance_groups,id'
        ];
    }
}
