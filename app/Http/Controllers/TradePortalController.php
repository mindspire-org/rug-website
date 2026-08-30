<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TradeProject;
use App\Models\TradeQuote;
use App\Models\SampleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradePortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [
            'active_projects' => $user->tradeProjects()->where('status', 'active')->count(),
            'pending_quotes'  => $user->tradeQuotes()->whereIn('status', ['draft', 'sent'])->count(),
            'samples_progress'=> $user->sampleRequests()->whereIn('status', ['pending', 'approved', 'shipped'])->count(),
            'orders_production'=> $user->orders()->whereIn('status', ['pending', 'processing', 'shipped'])->count(),
        ];
        $recentProjects = $user->tradeProjects()->latest()->take(5)->get();
        $discount = $user->trade_discount;

        return view('trade.dashboard', compact('stats', 'recentProjects', 'discount'));
    }

    public function projects()
    {
        $projects = Auth::user()->tradeProjects()->latest()->get();
        return view('trade.projects', compact('projects'));
    }

    public function createProject()
    {
        return view('trade.projects-create');
    }

    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'room' => 'nullable|string|max:255',
        ]);
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';
        TradeProject::create($validated);
        return redirect()->route('trade.portal.projects')->with('success', 'Project created successfully.');
    }

    public function editProject(TradeProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);
        return view('trade.projects-edit', compact('project'));
    }

    public function updateProject(Request $request, TradeProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'room' => 'nullable|string|max:255',
            'status' => 'required|in:active,archived,completed',
        ]);
        $project->update($validated);
        return redirect()->route('trade.portal.projects')->with('success', 'Project updated successfully.');
    }

    public function destroyProject(TradeProject $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);
        $project->delete();
        return redirect()->route('trade.portal.projects')->with('success', 'Project deleted successfully.');
    }

    public function quotes()
    {
        $quotes = Auth::user()->tradeQuotes()->with('project')->latest()->get();
        return view('trade.quotes', compact('quotes'));
    }

    public function createQuote()
    {
        $projects = Auth::user()->tradeProjects()->where('status', 'active')->latest()->get();
        return view('trade.quotes-create', compact('projects'));
    }

    public function storeQuote(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:trade_projects,id',
            'quote_number' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'items_count' => 'required|integer|min:1',
        ]);
        $project = TradeProject::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) abort(403);
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'draft';
        TradeQuote::create($validated);
        return redirect()->route('trade.portal.quotes')->with('success', 'Quote created successfully.');
    }

    public function editQuote(TradeQuote $quote)
    {
        if ($quote->user_id !== Auth::id()) abort(403);
        $projects = Auth::user()->tradeProjects()->where('status', 'active')->latest()->get();
        return view('trade.quotes-edit', compact('quote', 'projects'));
    }

    public function updateQuote(Request $request, TradeQuote $quote)
    {
        if ($quote->user_id !== Auth::id()) abort(403);
        $validated = $request->validate([
            'project_id' => 'required|exists:trade_projects,id',
            'quote_number' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'items_count' => 'required|integer|min:1',
            'status' => 'required|in:draft,sent,approved,expired',
        ]);
        $project = TradeProject::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) abort(403);
        $quote->update($validated);
        return redirect()->route('trade.portal.quotes')->with('success', 'Quote updated successfully.');
    }

    public function destroyQuote(TradeQuote $quote)
    {
        if ($quote->user_id !== Auth::id()) abort(403);
        $quote->delete();
        return redirect()->route('trade.portal.quotes')->with('success', 'Quote deleted successfully.');
    }

    public function printQuote(TradeQuote $quote)
    {
        if ($quote->user_id !== Auth::id()) abort(403);
        return view('trade.quotes-print', compact('quote'));
    }

    public function samples()
    {
        $samples = Auth::user()->sampleRequests()->with('product')->latest()->get();
        return view('trade.samples', compact('samples'));
    }

    public function createSample()
    {
        return view('trade.samples-create');
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->get();
        return view('trade.orders', compact('orders'));
    }

    public function account()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        return view('trade.account', compact('user', 'addresses'));
    }
}
