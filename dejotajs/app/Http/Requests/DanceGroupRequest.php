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
            'genre' => 'required|string|max:255',
            'picture' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',     // max 2MB
            'approval' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',   // max 5MB
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Kolektīva nosaukuma lauks ir obligāts.',
            'name.max' => 'Kolektīva nosaukumam jābūt ne vairāk kā 255 rakstzīmēm.',
            'description.required' => 'Lūdzu, uzrakstiet informāciju par kolektīvu.',
            'description.max' => 'Informācija par kolektīvu nevar būt garāka par 500 rakstzīmēm.',
            'city.required' => 'Lūdzu, norādiet kolektīva pilsētu.',
            'city.max' => 'Pilsētas lauks nevar būt garāks par 255 rakstzīmēm.',
            'address.required' => 'Lūdzu, norādiet kolektīva adresi.',
            'adress.max' => 'Adreses lauks nevar būt garāks par 255 rakstzīmēm.',
            'genre.required' => 'Lūdzu, norādiet kolektīva žanru.',
            'picture.mimes' => 'Attēla failam ir jābūt šādos formātos jpg, jpeg, png.',
            'approval.required' => 'Lūdzu pievienojiet dokomentu, kas apliecina, ka kolektīvs pastāv.',
            'approval.mimes' => 'Apstiprinājuma failam ir jābūt kādā no šiem formātiem: pdf, doc, docx, jpg, jpeg, png.'
        ];
    }

}
