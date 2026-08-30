@extends('layouts.admin')
@section('title', 'Sample Requests')

@section('admin-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Sample Requests</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Material sample requests from customers</p>
    </div>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">DATE</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CUSTOMER</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUG</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">COLOR</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TRACKING</th>
            </tr>
        </thead>
        <tbody>
            @foreach($samples as $s)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3" style="font-size:13px; color:rgba(18,18,18,0.55);">{{ $s->created_at->format('M j, Y') }}</td>
                <td class="px-4 py-3">
                    <p style="font-size:14px; font-weight:500; color:#121212;">{{ $s->user->name ?? '—' }}</p>
                    <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $s->user->email ?? '—' }}</p>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $s->rug_name ?? $s->product?->name ?? '—' }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $s->color ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:11px; font-weight:500; padding:3px 10px; border-radius:20px;
                        {{ match($s->status) {
                            'pending' => 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;',
                            'approved' => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
                            'shipped' => 'color:#7c3aed; background:#ede9fe; border:1px solid #ddd6fe;',
                            'delivered' => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
                            default => 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;',
                        } }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
                <td class="px-5 py-3" style="font-size:13px; color:rgba(18,18,18,0.55); font-family:monospace;">{{ $s->tracking_number ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-5 py-3 border-t border-stone-100">{{ $samples->links() }}</div>
    @if($samples->isEmpty())
    <div class="px-6 py-12 text-center">
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No sample requests yet.</p>
    </div>
    @endif
</div>

@endsection
