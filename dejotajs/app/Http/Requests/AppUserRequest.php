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

    public function messages(): array
    {
        return [
            'name.required' => 'Vārda lauks ir obligāts.',
            'name.max' => 'Vārdam jābūt ne vairāk kā 50 rakstzīmēm.',
            'surname.required' => 'Uzvārda lauks ir obligāts.',
            'surname.max' => 'Uzvārdam jābūt ne vairāk kā 50 rakstzīmēm.',
            'email.required' => 'E-pasta lauks ir obligāts.',
            'email.email' => 'E-pastam jābūt derīgā formātā.',
            'email.unique' => 'Šis e-pasts jau ir reģistrēts.',
            'password.required' => 'Paroles lauks ir obligāts.',
            'password.min' => 'Parolei jābūt vismaz 8 rakstzīmēm.',
            'password.max' => 'Parolei jābūt ne vairāk kā 50 rakstzīmēm.',
            'phone_number.required' => 'Telefona numura lauks ir obligāts.',
            'phone_number.max' => 'Telefona numuram jābūt ne vairāk kā 20 rakstzīmēm.',
        ];
    }
}
