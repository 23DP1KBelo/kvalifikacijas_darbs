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
            'dance_group_id' => 'required|integer|exists:dance_groups,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vecuma grupas nosaukuma lauks ir obligāts.',
            'name.max' => 'Vecuma grupas nosaukumam jābūt ne vairāk kā 255 rakstzīmēm.',
            'age_group.required' => 'Vecuma grupas lauks ir obligāts.',
            'age_group.max' => 'Vecuma grupas lauks jābūt ne vairāk kā 255 rakstzīmēm.',
            'dance_group_id.required' => 'Deju grupas ID lauks ir obligāts.',
        ];
    }
}
