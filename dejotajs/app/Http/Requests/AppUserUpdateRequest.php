<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppUserUpdateRequest extends FormRequest
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
        $user = $this->user();
        return [
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('app_users', 'email')->ignore($user->id),
            ],
            'phone_number' => 'required|min:8|max:12',
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
            'phone_number.required' => 'Telefona numura lauks ir obligāts.',
            'phone_number.min' => 'Telefona numuram jābūt vismaz 8 rakstzīmēm',
            'phone_number.max' => 'Telefona numuram jābūt ne vairāk kā 12 rakstzīmēm.',
        ];
    }
}
