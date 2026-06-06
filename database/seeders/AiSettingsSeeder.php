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
        SiteSetting::set('ai_api_key', env('OPENAI_API_KEY', ''), 'ai', 'string', 'Your OpenAI API key for room visualization');
        SiteSetting::set('ai_image_model', 'gpt-image-1', 'ai', 'string', 'OpenAI image model: gpt-image-1, gpt-image-1.5, gpt-image-2');
        SiteSetting::set('ai_image_size', '1024x1024', 'ai', 'string', 'Generated image size: 1024x1024, 1536x1024 (landscape), 1024x1536 (portrait)');
        SiteSetting::set('ai_image_quality', 'high', 'ai', 'string', 'Image quality: low, medium, high, auto');
    }
}
