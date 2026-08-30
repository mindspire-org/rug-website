<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeProject;
use App\Models\User;
use Illuminate\Http\Request;

class TradeProjectController extends Controller
{
    /**
     * List every trade project (across all trade accounts) and provide the form
     * for the admin to create/assign a new project to a trade user.
     */
    public function index()
    {
        $projects = TradeProject::with('user')->latest()->get();

        // Accounts a project can be assigned to: trade professionals (and team).
        $tradeUsers = User::whereIn('role', [User::ROLE_TRADE, User::ROLE_TEAM])
            ->orderBy('name')
            ->get();

        return view('admin.trade-projects.index', compact('projects', 'tradeUsers'));
    }

    /**
     * Create a project and assign it to the selected trade user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'name'        => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'room'        => 'nullable|string|max:255',
            'status'      => 'required|in:active,archived,completed',
            'rugs_count'  => 'nullable|integer|min:0',
            'total_value' => 'nullable|numeric|min:0',
        ]);

        TradeProject::create($validated);

        return back()->with('success', 'Project created and assigned successfully.');
    }

    /**
     * Update / reassign an existing project.
     */
    public function update(Request $request, TradeProject $tradeProject)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'name'        => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'room'        => 'nullable|string|max:255',
            'status'      => 'required|in:active,archived,completed',
            'rugs_count'  => 'nullable|integer|min:0',
            'total_value' => 'nullable|numeric|min:0',
        ]);

        $tradeProject->update($validated);

        return back()->with('success', 'Project updated successfully.');
    }

    public function destroy(TradeProject $tradeProject)
    {
        $tradeProject->delete();

        return back()->with('success', 'Project deleted.');
    }
}
