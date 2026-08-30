<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Auth::user()->wishlist()->with('product.primaryImage', 'product.colors')->get();
        return view('wishlist.index', compact('items'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $existing = Wishlist::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();
        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $request->product_id]);
            $added = true;
        }
        if ($request->expectsJson()) {
            return response()->json(['added' => $added]);
        }
        return back()->with('success', $added ? 'Added to wishlist!' : 'Removed from wishlist.');
    }

    public function remove(Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === Auth::id(), 403);
        $wishlist->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
