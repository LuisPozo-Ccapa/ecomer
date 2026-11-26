<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactoRequest extends FormRequest
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
            'nombre'       => 'required|string|max:255',
            'empresa'      => 'nullable|string|max:255',
            'telefono'     => 'required|string|max:50',
            'email'        => 'nullable|email|max:255',
            'sector'       => 'nullable|string|max:100',
            'producto'     => 'nullable|string|max:255',
            'mensaje'      => 'required|string|max:5000',
            'site_id'      => 'nullable|integer',
            'sitio_nombre' => 'nullable|string|max:255',
            'sitio_logo'   => 'nullable|string|max:1000',
            '_hp_website'  => 'nullable|string|max:255', // Campo trampa (Honeypot)
        ];
    }
}
