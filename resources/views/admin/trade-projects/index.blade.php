@extends('layouts.admin')
@section('title', 'Trade Projects')

@section('admin-content')
@php
$statusColors = [
    'active'    => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
    'archived'  => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
    'completed' => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
];
@endphp

<div x-data="{ editing: null, form: {} }">

    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Trade Projects</h1>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Create projects and assign them to trade accounts. Assigned projects appear in the trade user's portal.</p>
        </div>
    </div>

    @if($tradeUsers->isEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-lg px-5 py-4 mb-6" style="font-size:13px; color:#92400e;">
        No trade accounts exist yet. Create a trade account under <a href="{{ route('admin.trade-accounts.index') }}" style="text-decoration:underline;">Trade Accounts</a> before assigning projects.
    </div>
    @endif

    {{-- ── CREATE / ASSIGN FORM ── --}}
    <div class="bg-white border border-stone-200 rounded-lg p-6 mb-8">
        <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-5">Assign New Project</h3>
        <form action="{{ route('admin.trade-projects.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Assign to <span style="color:#ef4444;">*</span></label>
                    <select name="user_id" required
                            class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white" style="color:#0f172a;">
                        <option value="">— Select trade account —</option>
                        @foreach($tradeUsers as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} @if($u->company_name)({{ $u->company_name }})@endif</option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Project Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Riverside Penthouse"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                    @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Client Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name') }}" required placeholder="e.g. J. Smith"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                    @error('client_name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Room</label>
                    <input type="text" name="room" value="{{ old('room') }}" placeholder="e.g. Living Room"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Status</label>
                    <select name="status"
                            class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white" style="color:#0f172a;">
                        <option value="active" {{ old('status')==='active'?'selected':'' }}>Active</option>
                        <option value="archived" {{ old('status')==='archived'?'selected':'' }}>Archived</option>
                        <option value="completed" {{ old('status')==='completed'?'selected':'' }}>Completed</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Rugs</label>
                        <input type="number" name="rugs_count" value="{{ old('rugs_count', 0) }}" min="0"
                               class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Value ($)</label>
                        <input type="number" step="0.01" name="total_value" value="{{ old('total_value', 0) }}" min="0"
                               class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="inline-flex items-center gap-2 text-white rounded-lg" style="background:#E8651A; padding:10px 20px; font-size:14px; font-weight:500;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                    Assign Project
                </button>
            </div>
        </form>
    </div>

    {{-- ── PROJECTS TABLE ── --}}
    <div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                    <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PROJECT</th>
                    <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ASSIGNED TO</th>
                    <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CLIENT</th>
                    <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ROOM</th>
                    <th class="px-4 py-3 text-center" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUGS</th>
                    <th class="px-4 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">VALUE</th>
                    <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                    <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                    <td class="px-5 py-3">
                        <p style="font-size:14px; font-weight:500; color:#121212;">{{ $project->name }}</p>
                        <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $project->created_at->format('M j, Y') }}</p>
                    </td>
                    <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $project->user->name ?? '—' }}</td>
                    <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $project->client_name }}</td>
                    <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $project->room ?? '—' }}</td>
                    <td class="px-4 py-3 text-center" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $project->rugs_count }}</td>
                    <td class="px-4 py-3 text-right" style="font-size:14px; color:#121212;">${{ number_format($project->total_value, 0) }}</td>
                    <td class="px-4 py-3">
                        <span style="font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; {{ $statusColors[$project->status] ?? $statusColors['active'] }}; text-transform:capitalize;">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <button type="button" class="text-xs px-2 py-1 rounded border border-stone-200 hover:bg-stone-50" style="color:#121212;"
                                @click='editing = {{ $project->id }}; form = {{ \Illuminate\Support\Js::from([
                                    "user_id" => $project->user_id,
                                    "name" => $project->name,
                                    "client_name" => $project->client_name,
                                    "room" => $project->room,
                                    "status" => $project->status,
                                    "rugs_count" => $project->rugs_count,
                                    "total_value" => $project->total_value,
                                ]) }}'>
                            Edit
                        </button>
                        <form action="{{ route('admin.trade-projects.destroy', $project) }}" method="POST" class="inline"
                              onsubmit="return confirm('Delete this project? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs px-2 py-1 rounded border border-stone-200 hover:bg-red-50 hover:text-red-600" style="color:#121212;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($projects->isEmpty())
        <div class="px-6 py-12 text-center">
            <p style="font-size:14px; color:rgba(18,18,18,0.55);">No projects yet. Assign one using the form above.</p>
        </div>
        @endif
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div x-show="editing !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(15,23,42,0.55);"
         @keydown.escape.window="editing = null">
        <div class="bg-white rounded-xl w-full max-w-lg p-6" @click.outside="editing = null">
            <div class="flex items-center justify-between mb-5">
                <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;">Edit Project</h3>
                <button type="button" @click="editing = null" class="text-stone-400 hover:text-stone-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'{{ url('admin/trade-projects') }}/' + editing" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Assign to</label>
                        <select name="user_id" x-model="form.user_id" required
                                class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white" style="color:#0f172a;">
                            @foreach($tradeUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} @if($u->company_name)({{ $u->company_name }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Project Name</label>
                            <input type="text" name="name" x-model="form.name" required
                                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Client Name</label>
                            <input type="text" name="client_name" x-model="form.client_name" required
                                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Room</label>
                            <input type="text" name="room" x-model="form.room"
                                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Status</label>
                            <select name="status" x-model="form.status"
                                    class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white" style="color:#0f172a;">
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Rugs</label>
                            <input type="number" name="rugs_count" x-model="form.rugs_count" min="0"
                                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Value ($)</label>
                            <input type="number" step="0.01" name="total_value" x-model="form.total_value" min="0"
                                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400" style="color:#0f172a;">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="editing = null" class="px-4 py-2 text-sm rounded-lg border border-stone-200 hover:bg-stone-50" style="color:#374151;">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm text-white rounded-lg" style="background:#E8651A;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
