<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        return response()->json($this->settingsService->all());
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'app_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048', // 2MB max
            'favicon' => 'nullable|image|max:1024', // 1MB max
            // Add more validation rules for other settings
        ]);

        foreach ($validatedData as $key => $value) {
            if ($request->hasFile($key)) {
                // Delete old file if exists
                $oldFile = $this->settingsService->get($key);
                if ($oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }

                // Store new file
                $path = $request->file($key)->store('settings', 'public');
                $this->settingsService->set($key, $path);
            } else {
                $this->settingsService->set($key, $value);
            }
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
    public function show($key)
{
    return response()->json([
        $key => settings($key)
    ]);
}
}
