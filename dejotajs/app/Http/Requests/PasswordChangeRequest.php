<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class PasswordChangeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed']
        ];
    }

    #[Override]
    public function messages(): array{
        return [
            'current_password' => 'Ievadiet pašreizējo paroli',
            'password.required' => 'Ievadiet jauno paroli',
            'password.min' => 'Parolei jābūt vismaz 8 simboliem',
            'password.confirmed' => 'Paroles nesakrīt.',
        ];
    }
}
