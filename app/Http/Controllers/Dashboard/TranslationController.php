<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TranslationController extends Controller
{
    private function refreshCaches()
    {
        Artisan::call('language:refresh');
        Artisan::call('translations:refresh');
    }

    /**
     * Display a listing of the translations for a specific language.
     */
    public function index($locale)
    {
        $language = Language::where('code', $locale)->where('is_active', true)->firstOrFail();
        $translations = Translation::where('locale', $locale)->get()
            ->pluck('text', 'key');
        return response()->json($translations);
    }

    /**
     * Store a newly created translation in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'key' => 'required|string',
            'text' => 'required|string',
            'locale' => 'required|string|exists:languages,code,is_active,1',
        ]);

        $translation = Translation::updateOrCreate(
            [
                'key' => $validatedData['key'],
                'locale' => $validatedData['locale']
            ],
            [
                'text' => $validatedData['text']
            ]
        );

        $this->refreshCaches();

        $action = $translation->wasRecentlyCreated ? 'created' : 'updated';

        return response()->json([
            'message' => "Translation {$action} successfully",
            'translation' => $translation
        ], $translation->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Update the specified translation in storage.
     */
    public function update(Request $request, $key, $locale)
    {
        $translation = Translation::where('key', $key)
            ->where('locale', $locale)
            ->firstOrFail();

        $validatedData = $request->validate([
            'text' => 'required|string',
        ]);

        $translation->update($validatedData);
        $this->refreshCaches();

        return response()->json([
            'message' => 'Translation updated successfully',
            'translation' => $translation
        ]);
    }

    /**
     * Remove the specified translation from storage.
     */
    public function destroy($key, $locale)
    {
        $translation = Translation::where('key', $key)
            ->where('locale', $locale)
            ->firstOrFail();

        $translation->delete();
        $this->refreshCaches();

        return response()->json([
            'message' => 'Translation deleted successfully'
        ]);
    }

    /**
     * Bulk update translations for a specific language.
     */
    public function bulkUpdate(Request $request, $locale)
    {
        $validatedData = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|string',
        ]);

        foreach ($validatedData['translations'] as $key => $text) {
            Translation::updateOrCreate(
                ['key' => $key, 'locale' => $locale],
                ['text' => $text]
            );
        }

        $this->refreshCaches();

        return response()->json([
            'message' => 'Translations updated successfully'
        ]);
    }
}
