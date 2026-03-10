<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdmissionRequest extends FormRequest
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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'age_group_id' => 'required|integer|exists:age_groups,id'
            
        ];
    }

    public function messages()
    {
         return [
            'name.required' => 'Vārda lauks ir obligāts.',
            'name.max' => 'Vārdam jābūt ne vairāk kā 50 rakstzīmēm.',
            'start_date.required' => 'Norādiet sakuma datumu uzņemšanai.',
            'start_date.date' => 'Norādiet datuma formātā lauku.',
            'end_date.required' => 'Norādiet beigu datumu uzņemšanai.',
            'end_date.date' => 'Norādiet datuma formātā lauku.',
            'age_group_id.integer' => 'Vecuma grupas ID jābūt veselam skaitlim.',
            'age_group_id.exists' => 'Norādītā vecuma grupa neeksistē.',
            'age_group_id.required' => 'Vecuma grupai jabūt norādītai.'
        ];
    }
}
