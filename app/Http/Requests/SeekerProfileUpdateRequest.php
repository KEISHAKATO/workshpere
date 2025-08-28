<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeekerProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'about'            => 'nullable|string|max:2000',
            'skills'           => 'nullable|array',
            'skills.*'         => 'string|max:60',
            'years_experience' => 'nullable|integer|min:0|max:60',
            'city'             => 'nullable|string|max:120',
            'county'           => 'nullable|string|max:120',
            'lat'              => 'nullable|numeric',
            'lng'              => 'nullable|numeric',
        ];
    }
}
