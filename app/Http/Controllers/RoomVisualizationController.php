<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RoomVisualization;
use App\Services\AiImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoomVisualizationController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please log in to use See It In Your Room.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to use See It In Your Room.');
        }

        $user = Auth::user();

        if ($user->ai_credits <= 0) {
            $msg = 'You have used all your AI room visualization credits. Please contact support for more.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $msg, 'credits' => 0], 422);
            }
            return back()->with('error', $msg);
        }

        $request->validate([
            'room_photo' => 'required|image|max:10240',
        ]);

        // AI image generation can take well over 30s; lift the per-request limit
        // (the built-in server and most FPM setups default to 30s).
        @set_time_limit(300);
        @ignore_user_abort(true);

        // Store room photo
        $roomPath = $request->file('room_photo')->store('room_photos', 'public');

        // Create visualization record
        $viz = RoomVisualization::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'room_photo_path' => $roomPath,
            'status' => 'processing',
            'prompt' => "Place {$product->name} rug into the uploaded room photo",
        ]);

        // Deduct credit
        $user->decrement('ai_credits');

        // Generate AI image
        $service = new AiImageService();
        $result = $service->generateRoomVisualization(
            $product->primary_image_url,
            $roomPath,
            $product->name
        );

        if ($result['success']) {
            $viz->update([
                'generated_image_path' => $result['path'],
                'status' => 'completed',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                    'credits' => $user->fresh()->ai_credits,
                ]);
            }

            return back()->with('success', 'Your room visualization has been generated!')
                ->with('room_viz_url', $result['url']);
        }

        // Refund credit on failure
        $user->increment('ai_credits');
        $viz->update([
            'status' => 'failed',
            'error_message' => $result['error'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'credits' => $user->fresh()->ai_credits,
            ], 422);
        }

        return back()->with('error', $result['error']);
    }

    /**
     * Stream a generated visualization back as a file attachment. The bare
     * `download` attribute on an <a> is ignored for cross-origin URLs and the
     * /storage/ path 404s when the symlink is missing, so serve it explicitly.
     */
    public function download(RoomVisualization $visualization)
    {
        if (!Auth::check() || $visualization->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visualization->generated_image_path) {
            abort(404);
        }

        $candidates = [
            storage_path('app/public/' . $visualization->generated_image_path),
            public_path('storage/' . $visualization->generated_image_path),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return response()->download($path, 'costikyan-room-visualization-' . $visualization->id . '.png');
            }
        }

        abort(404);
    }

    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $visualizations = Auth::user()->roomVisualizations()
            ->with('product')
            ->latest()
            ->paginate(12);

        return view('room-visualizations.index', compact('visualizations'));
    }
}
