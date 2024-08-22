<?php

namespace App\Console\Commands;

use App\Models\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshLanguageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the language cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('active_languages');
        $languages = Language::where('is_active', true)->pluck('code')->toArray();
        Cache::put('active_languages', $languages, 60*24);

        $this->info('Language cache refreshed successfully.');
    }
}
