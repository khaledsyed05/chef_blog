<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
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
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'youtube_video' => 'nullable|url',
            'cover_image' => 'nullable|image|max:2048',
            'featured' => 'boolean',
            'published' => 'boolean',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'ingredients' => 'required|array',
            'ingredients.*' => 'required',
            'instructions' => 'required|array',
            'instructions.*' => 'required',
            'total_time' => 'required|array',
            'total_time.*' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];

        return $rules;
    }
}
