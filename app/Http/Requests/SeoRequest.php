<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeoRequest extends FormRequest
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
        $rules = [];
        $locales = config('translatable.locales');

        foreach ($locales as $locale) {
            $rules["seo.meta_title.$locale"] = 'nullable|string|max:60';
            $rules["seo.meta_description.$locale"] = 'nullable|string|max:160';
            $rules["seo.og_title.$locale"] = 'nullable|string|max:60';
            $rules["seo.og_description.$locale"] = 'nullable|string|max:160';
        }

        return $rules;
    }
}
