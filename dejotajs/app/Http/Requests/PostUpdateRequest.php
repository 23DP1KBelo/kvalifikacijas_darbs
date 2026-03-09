<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Lūdzu, ievadiet ieraksta nosaukumu.',
            'title.string' => 'Ieraksta nosaukumam jābūt tekstam.',
            'title.max' => 'Ieraksta nosaukums nedrīkst pārsniegt 255 rakstzīmes.',
            'description.required' => 'Lūdzu, ievadiet ieraksta aprakstu.',
            'description.string' => 'Ieraksta aprakstam jābūt tekstam.'
        ];
    }
}
