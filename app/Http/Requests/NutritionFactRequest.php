<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NutritionFactRequest extends FormRequest
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
        $locales = config('translatable.locales');
        $rules = [];

        foreach ($locales as $locale) {
            $rules["nutrition_fact.$locale"] = 'nullable|array';
            $rules["nutrition_fact.$locale.calories"] = 'nullable';
            $rules["nutrition_fact.$locale.protein"] = 'nullable';
            $rules["nutrition_fact.$locale.fat"] = 'nullable';
        }

        return $rules;
    }
}
