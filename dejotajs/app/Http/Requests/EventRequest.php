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
            'age_group_ids' => 'array',
            'age_group_ids.*' => 'exists:age_groups,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Pasākuma nosaukums lauks ir obligāts.',
            'name.max' => 'Pasākuma nosaukumam jābūt līdz 255 simboliem.',
            'description.max' => 'Aprakstam jabūt līdz 500 simboliem.',
            'date_start.required' => 'Norādiet sākuma datumu un laiku,',
            'date_end.required' => 'Norādiet beigu datumu un laiku',
            'dance_group_member_id' => 'Nav norādīts veidotājs',
        ];
    }
}
