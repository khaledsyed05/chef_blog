<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;

class LanguageController extends Controller
{
    private function refreshLanguageCache()
    {
        Artisan::call('language:refresh');
    }
    /**
     * Display a listing of the languages.
     */
    public function index()
    {
        $languages = Language::all();
        return response()->json($languages);
    }

    /**
     * Store a newly created language in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:languages,code',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        
    if (isset($validatedData['is_default']) && $validatedData['is_default']) {
        Language::where('is_default', true)->update(['is_default' => false]);
    }

    $language = Language::create($validatedData);

    $this->refreshLanguageCache();

    return response()->json([
        'message' => 'Language created successfully',
        'language' => $language
    ], 201);

    }

    /**
     * Display the specified language.
     */
    public function show(Language $language)
    {
        return response()->json($language);
    }

    /**
     * Update the specified language in storage.
     */
    public function update(Request $request, Language $language)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'code' => [
                'string',
                'max:5',
                Rule::unique('languages')->ignore($language->id),
            ],
            'is_active' => 'boolean', 'is_default' => 'boolean',
        ]);
    
        if (isset($validatedData['is_default']) && $validatedData['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }
    
        $language->update($validatedData);
    
        $this->refreshLanguageCache();
    
        return response()->json([
            'message' => 'Language updated successfully',
            'language' => $language
        ]);
    }

    /**
     * Remove the specified language from storage.
     */
    public function destroy(Language $language)
    {
        $language->delete();
        $this->refreshLanguageCache();
        return response()->json(['message' => 'Language deleted successfully']);
    }

    /**
     * Activate or deactivate a language.
     */
    public function toggleActive(Language $language)
    {
        $language->is_active = !$language->is_active;
        $language->save();
        $this->refreshLanguageCache();

        $status = $language->is_active ? 'activated' : 'deactivated';
        return response()->json([
            'message' => "Language {$status} successfully",
            'language' => $language
        ]);
    }
}
