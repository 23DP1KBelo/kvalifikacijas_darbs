<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'title' => 'required|max:255',
            'description' => 'max:500',
            'picture' => 'nullable|image|max:2048',
            'private' => 'required|boolean',
            'dance_group_member_id' => 'integer|exists:dance_group_members,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Lūdzu, ievadiet ieraksta nosaukumu.',
            'title.max' => 'Ieraksta nosaukums nedrīkst pārsniegt 255 rakstzīmes.',
            'description.max' => 'Ieraksta apraksts nedrīkst pārsniegt 500 rakstzīmes.',
            'picture.image' => 'Pievienotajam failam jābūt attēlam (jpg, jpeg, png).',
            'picture.max' => 'Attēla faila izmērs nedrīkst pārsniegt 2MB.',
            'private.required' => 'Lūdzu, norādiet vai ieraksts ir privāts vai publisks.',
            'private.boolean' => 'Privāts lauks jānorāda kā true (1) vai false (0).',
            'dance_group_member_id.integer' => 'Deju grupas dalībnieka ID jābūt veselam skaitlim.',
            'dance_group_member_id.exists' => 'Norādītais deju grupas dalībnieks neeksistē.'
        ];
    }
}

