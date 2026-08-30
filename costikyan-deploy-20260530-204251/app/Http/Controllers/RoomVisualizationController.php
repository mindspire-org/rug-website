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
            return redirect()->route('login')->with('error', 'Please log in to use See It In Your Room.');
        }

        $user = Auth::user();

        if ($user->ai_credits <= 0) {
            return back()->with('error', 'You have used all your AI room visualization credits. Please contact support for more.');
        }

        $request->validate([
            'room_photo' => 'required|image|max:10240',
        ]);

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
            return back()->with('success', 'Your room visualization has been generated!')
                ->with('room_viz_url', $result['url']);
        } else {
            // Refund credit on failure
            $user->increment('ai_credits');
            $viz->update([
                'status' => 'failed',
                'error_message' => $result['error'],
            ]);
            return back()->with('error', $result['error']);
        }
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
