<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->booted(function () {
            $this->setLocale();
        });
    }

    protected function setLocale()
    {
        $languages = Cache::remember('active_languages', 60*24, function () {
            return Language::where('is_active', true)->pluck('code')->toArray();
        });

        config(['translatable.locales' => $languages]);
        config(['app.locales' => $languages]);

        $defaultLocale = Cache::remember('default_language', 60*24, function () {
            return Language::where('is_active', true)->where('is_default', true)->value('code') ?? 'en';
        });

        config(['app.locale' => $defaultLocale]);
        app()->setLocale($defaultLocale);

        Log::info('Locale set in LanguageServiceProvider', ['locale' => $defaultLocale]);
    }
}
