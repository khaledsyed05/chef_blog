<?php

use Illuminate\Support\Facades\Cache;


if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        $settings = app(App\Services\SettingsService::class);
        if (is_null($key)) {
            return $settings;
        }
        return $settings->get($key, $default);
    }
    if (!function_exists('cached_trans')) {
        function cached_trans($key, $locale = null)
        {
            $locale = $locale ?: app()->getLocale();
            $translations = Cache::get("translations.{$locale}", []);
            return $translations[$key] ?? $key;
        }
    }
}
