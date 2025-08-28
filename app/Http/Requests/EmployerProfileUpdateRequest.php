<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployerProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:160',
            'about'        => 'nullable|string|max:2000',
            'website'      => 'nullable|url|max:200',
            'city'         => 'nullable|string|max:120',
            'county'       => 'nullable|string|max:120',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
        ];
    }
}
