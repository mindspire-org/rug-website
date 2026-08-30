<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        User::whereNull('ai_credits')->orWhere('ai_credits', 0)->update(['ai_credits' => 3]);

        SiteSetting::set('ai_provider', 'openai', 'ai', 'string', 'AI provider: openai, replicate, etc.');
        SiteSetting::set('ai_api_key', '', 'ai', 'string', 'Your OpenAI API key for room visualization');
    }
}
