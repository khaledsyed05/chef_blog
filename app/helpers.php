<?php

if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        $settings = app(App\Services\SettingsService::class);
        if (is_null($key)) {
            return $settings;
        }
        return $settings->get($key, $default);
    }
}