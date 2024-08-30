<?php

namespace App\Console\Commands;

use App\Models\Translation;
use App\Models\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshTranslationCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the translations cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear existing translations cache
        Cache::forget('translations');

        // Get active languages
        $activeLanguages = Language::where('is_active', true)->pluck('code');

        // Fetch all translations for active languages and group them by locale
        $translations = Translation::whereIn('locale', $activeLanguages)->get()->groupBy('locale');

        // Cache the translations
        foreach ($translations as $locale => $localeTranslations) {
            $translationsArray = $localeTranslations->pluck('text', 'key')->toArray();
            Cache::put("translations.{$locale}", $translationsArray, 60 * 24); // Cache for 24 hours
        }

        $this->info('Translations cache has been refreshed.');
    }
}
