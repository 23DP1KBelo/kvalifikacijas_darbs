<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'description' => 'max:500',
            'location' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'dance_group_member_id' => 'required|exists:dance_group_members,id',
            'age_group_ids' => 'required|array',
            'age_group_ids.*' => 'exists:age_groups,id',
        ];
    }
}
