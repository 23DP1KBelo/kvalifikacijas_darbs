<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DanceGroupRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'gener' => 'required|string|max:255',
            'picture' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',     // max 2MB
            'approval' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',   // max 5MB
        ];
    }

}
