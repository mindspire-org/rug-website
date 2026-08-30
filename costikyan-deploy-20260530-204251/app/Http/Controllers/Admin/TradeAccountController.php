<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TradeAccountController extends Controller
{
    public function index()
    {
        $accounts = User::where('role', User::ROLE_TRADE)->orWhereNotNull('trade_discount')->latest()->get();
        return view('admin.trade-accounts.index', compact('accounts'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:' . User::ROLE_TRADE . ',' . User::ROLE_CLIENT,
            'trade_discount' => 'required|numeric|min:0|max:100',
            'company_name' => 'nullable|string|max:255',
        ]);

        $user->update($validated);
        return back()->with('success', 'Trade account updated successfully.');
    }

    public function toggleTrade(User $user)
    {
        $newRole = $user->role === User::ROLE_TRADE ? User::ROLE_CLIENT : User::ROLE_TRADE;
        $user->update(['role' => $newRole]);
        return back()->with('success', 'Trade status toggled.');
    }
}
