<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiImageService
{
    public function generateRoomVisualization(string $productImageUrl, string $roomPhotoPath, string $productName): array
    {
        $provider = SiteSetting::get('ai_provider', 'openai');
        $apiKey = SiteSetting::get('ai_api_key');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'error' => 'AI provider not configured. Please set your API key in Admin > Settings.',
            ];
        }

        if ($provider === 'openai') {
            return $this->generateWithOpenAi($productImageUrl, $roomPhotoPath, $productName, $apiKey);
        }

        return [
            'success' => false,
            'error' => 'Unsupported AI provider: ' . $provider,
        ];
    }

    private function generateWithOpenAi(string $productImageUrl, string $roomPhotoPath, string $productName, string $apiKey): array
    {
        try {
            // Convert room photo to base64
            $roomFullPath = storage_path('app/public/' . $roomPhotoPath);
            if (!file_exists($roomFullPath)) {
                return ['success' => false, 'error' => 'Room photo not found.'];
            }
            $roomBase64 = base64_encode(file_get_contents($roomFullPath));
            $roomMime = mime_content_type($roomFullPath);

            // Get product image base64
            $productPath = null;
            // Try to extract storage path from URL
            if (str_contains($productImageUrl, '/storage/')) {
                $relative = parse_url($productImageUrl, PHP_URL_PATH);
                $relative = ltrim(str_replace('/storage/', '', $relative), '/');
                $productPath = storage_path('app/public/' . $relative);
            }

            $productBase64 = '';
            if ($productPath && file_exists($productPath)) {
                $productBase64 = base64_encode(file_get_contents($productPath));
            }

            $prompt = "Place this {$productName} rug realistically into the provided room photo. "
                . "Maintain the room's lighting, perspective, and style. "
                . "The rug should look naturally placed on the floor. "
                . "Keep the room's furniture and decor intact. "
                . "High quality, photorealistic result.";

            $content = [
                ['type' => 'text', 'text' => $prompt],
                ['type' => 'image_url', 'image_url' => ['url' => "data:{$roomMime};base64,{$roomBase64}"]],
            ];

            if ($productBase64) {
                $productMime = mime_content_type($productPath);
                $content[] = ['type' => 'image_url', 'image_url' => ['url' => "data:{$productMime};base64,{$productBase64}"]];
            }

            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'user', 'content' => $content],
                    ],
                    'max_tokens' => 1024,
                ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['success' => false, 'error' => 'AI generation failed. Please check your API key and try again.'];
            }

            $data = $response->json();
            // GPT-4o doesn't generate images directly, so we use DALL-E 3 for generation
            // Actually, for room visualization, DALL-E 3 with image editing is better
            return $this->generateWithDalle($productImageUrl, $roomPhotoPath, $productName, $apiKey);

        } catch (\Throwable $e) {
            Log::error('AI service error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'An error occurred during AI generation: ' . $e->getMessage()];
        }
    }

    private function generateWithDalle(string $productImageUrl, string $roomPhotoPath, string $productName, string $apiKey): array
    {
        try {
            $roomFullPath = storage_path('app/public/' . $roomPhotoPath);
            $roomBase64 = base64_encode(file_get_contents($roomFullPath));

            $prompt = "A photorealistic interior design visualization of a room with a {$productName} rug placed naturally on the floor. "
                . "Reference the uploaded room photo for the exact room layout, furniture, lighting, and style. "
                . "Place the rug centrally on the floor area. Keep all existing furniture. "
                . "High-end interior photography style, natural lighting, 4K quality.";

            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                    'quality' => 'hd',
                ]);

            if (!$response->successful()) {
                Log::error('DALL-E API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['success' => false, 'error' => 'AI image generation failed. Please check your API key and try again.'];
            }

            $data = $response->json();
            $imageUrl = $data['data'][0]['url'] ?? null;

            if (!$imageUrl) {
                return ['success' => false, 'error' => 'No image was generated.'];
            }

            // Download and save the generated image
            $imageContent = Http::get($imageUrl)->body();
            $filename = 'room_viz/' . Str::random(40) . '.png';
            Storage::disk('public')->put($filename, $imageContent);

            return [
                'success' => true,
                'path' => $filename,
                'url' => asset('storage/' . $filename),
            ];

        } catch (\Throwable $e) {
            Log::error('DALL-E service error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'An error occurred during AI generation: ' . $e->getMessage()];
        }
    }
}
