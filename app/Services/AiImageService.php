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
        $apiKey = SiteSetting::get('ai_api_key') ?: env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'error' => 'AI provider not configured. Please set your OPENAI_API_KEY in .env or Admin > Settings.',
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

    /**
     * Composite the rug into the user's real room photo using the OpenAI image
     * edit endpoint (gpt-image-1 family). Passing the actual room photo (and the
     * rug image when available) keeps the customer's real furniture, lighting and
     * perspective instead of inventing a new room from a text description.
     */
    private function generateWithOpenAi(string $productImageUrl, string $roomPhotoPath, string $productName, string $apiKey): array
    {
        try {
            $roomFullPath = storage_path('app/public/' . $roomPhotoPath);
            if (!file_exists($roomFullPath)) {
                return ['success' => false, 'error' => 'Room photo not found.'];
            }

            // OpenAI's gpt-image-1 edit endpoint requires PNG or WebP. Convert
            // the room photo and rug image to PNG to avoid "invalid image file"
            // errors when the source is JPEG (which most uploads/product images are).
            $roomBytes = $this->convertToPng($roomFullPath);
            if ($roomBytes === null) {
                return ['success' => false, 'error' => 'Could not process the room photo. Please upload a valid PNG, JPG, or WebP image.'];
            }

            // Resolve the rug image bytes (optional but strongly improves the result).
            $rugBytes = $this->loadProductImage($productImageUrl);
            if ($rugBytes !== null) {
                $rugPng = $this->convertBytesToPng($rugBytes);
                if ($rugPng !== null) {
                    $rugBytes = $rugPng;
                } else {
                    // Conversion failed — skip the rug image rather than fail the
                    // whole request; the prompt-only path still works.
                    Log::warning('Could not convert rug image to PNG, sending prompt only', ['url' => $productImageUrl]);
                    $rugBytes = null;
                }
            }

            $model   = SiteSetting::get('ai_image_model', 'gpt-image-1') ?: 'gpt-image-1';
            $size    = SiteSetting::get('ai_image_size', '1024x1024') ?: '1024x1024';
            $quality = SiteSetting::get('ai_image_quality', 'high') ?: 'high';

            if ($rugBytes) {
                $prompt = "You are compositing a product into a real room photo. The FIRST image is the customer's actual room. "
                    . "The SECOND image is a {$productName} area rug shown flat (top-down). Produce ONE photorealistic image of "
                    . "the SAME room with that rug laid on the floor. Strict requirements: "
                    . "(1) Keep the room's walls, windows, furniture, existing flooring, camera angle and lighting EXACTLY as in "
                    . "the first image — do not redesign, move, recolor or re-render anything else. "
                    . "(2) Lay the rug FLAT on the floor following the floor's real perspective and vanishing lines so it recedes "
                    . "naturally into the scene (never a flat rectangle facing the camera). "
                    . "(3) Preserve the rug's exact colors, pattern, border and texture from the second image. "
                    . "(4) Add realistic soft contact shadows under the rug edges, and let any furniture legs that fall on the rug "
                    . "sit on top of it with correct occlusion. "
                    . "(5) Size and position the rug realistically for the room (e.g. centered under/around the main furniture). "
                    . "The result must look like an unedited photo of the real room that simply has this rug in it.";
            } else {
                $prompt = "This is a photo of the customer's real room. Add a {$productName} area rug laid FLAT on the floor, "
                    . "following the floor's real perspective and vanishing lines so it recedes naturally into the scene. "
                    . "Keep the walls, windows, furniture, camera angle and lighting EXACTLY as they are — do not redesign the room. "
                    . "Add realistic soft contact shadows under the rug, with correct occlusion where furniture sits on it, and size "
                    . "it realistically. The result must look like an unedited photo of the real room that simply has this rug in it.";
            }

            $request = Http::withToken($apiKey)
                ->timeout(180)
                ->attach('image[]', $roomBytes, 'room.png');

            if ($rugBytes) {
                $request = $request->attach('image[]', $rugBytes, 'rug.png');
            }

            $payload = [
                'model'   => $model,
                'prompt'  => $prompt,
                'size'    => $size,
                'quality' => $quality,
                'n'       => 1,
            ];

            // input_fidelity=high keeps the real room (and rug pattern) faithful rather
            // than re-imagining them. Supported by the gpt-image-1 family.
            if (str_starts_with($model, 'gpt-image')) {
                $payload['input_fidelity'] = 'high';
            }

            $response = $request->post('https://api.openai.com/v1/images/edits', $payload);

            if (!$response->successful()) {
                Log::error('OpenAI image edit error', ['status' => $response->status(), 'body' => $response->body()]);
                $apiMessage = $response->json()['error']['message'] ?? null;
                return [
                    'success' => false,
                    'error' => 'AI image generation failed: ' . ($apiMessage ?: 'please check your API key and try again.'),
                ];
            }

            // gpt-image-* always returns base64, never a URL.
            $b64 = $response->json()['data'][0]['b64_json'] ?? null;
            if (!$b64) {
                return ['success' => false, 'error' => 'No image was generated.'];
            }

            $imageContent = base64_decode($b64);
            $filename = 'room_viz/' . Str::random(40) . '.png';
            Storage::disk('public')->put($filename, $imageContent);

            return [
                'success' => true,
                'path' => $filename,
                // /media/{path} rather than /storage/{path}: works without the symlink.
                'url' => route('media.show', ['path' => $filename]),
            ];

        } catch (\Throwable $e) {
            Log::error('AI service error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'An error occurred during AI generation: ' . $e->getMessage()];
        }
    }

    /**
     * Resolve the raw bytes of a product image from its public URL.
     * Handles the app's /media/{path} route, /storage/ URLs, and remote URLs.
     */
    private function loadProductImage(string $productImageUrl): ?string
    {
        $path = parse_url($productImageUrl, PHP_URL_PATH) ?: $productImageUrl;

        // Extract the storage-relative path from /media/{path} or /storage/{path}.
        $relative = null;
        if (str_contains($path, '/media/')) {
            $relative = ltrim(substr($path, strpos($path, '/media/') + strlen('/media/')), '/');
        } elseif (str_contains($path, '/storage/')) {
            $relative = ltrim(substr($path, strpos($path, '/storage/') + strlen('/storage/')), '/');
        }

        if ($relative) {
            $relative = urldecode($relative);
            foreach ([public_path('storage/' . $relative), storage_path('app/public/' . $relative)] as $candidate) {
                if (is_file($candidate) && filesize($candidate) > 100) {
                    return file_get_contents($candidate);
                }
            }
        }

        // Fall back to fetching a fully-qualified remote URL.
        if (filter_var($productImageUrl, FILTER_VALIDATE_URL) && str_starts_with($productImageUrl, 'http')) {
            try {
                $resp = Http::timeout(60)->get($productImageUrl);
                if ($resp->successful() && strlen($resp->body()) > 100) {
                    return $resp->body();
                }
            } catch (\Throwable $e) {
                Log::warning('Could not fetch product image', ['url' => $productImageUrl, 'error' => $e->getMessage()]);
            }
        }

        return null;
    }

    /**
     * Convert an image file to PNG bytes. Uses Imagick when available (better
     * quality for large images), falls back to GD. Returns null on failure.
     */
    private function convertToPng(string $filePath): ?string
    {
        if (!is_file($filePath)) {
            return null;
        }

        // Already PNG — return as-is.
        $mime = mime_content_type($filePath);
        if ($mime === 'image/png') {
            return file_get_contents($filePath);
        }

        return $this->convertBytesToPng(file_get_contents($filePath));
    }

    /**
     * Convert raw image bytes (JPEG, WebP, PNG, GIF, BMP) to PNG bytes.
     * Returns null on failure.
     */
    private function convertBytesToPng(string $bytes): ?string
    {
        // Already PNG.
        if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n")) {
            return $bytes;
        }

        // Prefer Imagick (handles WebP, large images, and is more robust).
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->readImageBlob($bytes);
                $imagick->setImageFormat('png');
                $imagick->setImageBackgroundColor(new \ImagickPixel('transparent'));
                $png = $imagick->getImageBlob();
                $imagick->clear();
                return $png;
            } catch (\Throwable $e) {
                Log::warning('Imagick PNG conversion failed, trying GD', ['error' => $e->getMessage()]);
            }
        }

        // Fall back to GD.
        if (function_exists('imagecreatefromstring')) {
            $im = @imagecreatefromstring($bytes);
            if ($im !== false) {
                // Preserve alpha channel for transparent PNGs.
                imagesavealpha($im, true);
                ob_start();
                imagepng($im, null, 6);
                $png = ob_get_clean();
                imagedestroy($im);
                return $png ?: null;
            }
        }

        return null;
    }
}
