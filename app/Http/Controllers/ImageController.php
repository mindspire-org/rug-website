<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    /**
     * Serve a file from storage/app/public/ (or public/storage/).
     * Uses native filesystem calls so it works regardless of the installed
     * Flysystem/Laravel version on shared hosting, and bypasses broken
     * public/storage symlinks.
     */
    public function show(Request $request, $path)
    {
        // Sanitize: no directory traversal
        $path = ltrim($path, '/');
        $path = str_replace(['..', "\\", "\x00"], '', $path);

        $candidates = [
            storage_path('app/public/' . $path),
            public_path('storage/' . $path),
        ];

        $full = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                $full = $candidate;
                break;
            }
        }

        if ($full === null) {
            Log::warning('Image not found', ['path' => $path]);
            abort(404);
        }

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $map = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
            'avif' => 'image/avif', 'bmp' => 'image/bmp', 'ico' => 'image/x-icon',
        ];
        $mime = $map[$ext]
            ?? (function_exists('mime_content_type') ? (mime_content_type($full) ?: 'application/octet-stream') : 'application/octet-stream');

        return response()->file($full, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
