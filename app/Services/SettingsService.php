<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected $settings = [];

    public function __construct()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        // Cache settings for 24 hours
        $this->settings = Cache::remember('app_settings', 60 * 24, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function set($key, $value)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        $this->settings[$key] = $value;
        $this->updateCache();
    }

    public function all()
    {
        return $this->settings;
    }

    protected function updateCache()
    {
        Cache::put('app_settings', $this->settings, 60 * 24);
    }
}