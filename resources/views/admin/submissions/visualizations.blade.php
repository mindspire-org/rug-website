@extends('layouts.admin')
@section('title', 'Room Visualizations')

@section('admin-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Room Visualizations</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">AI-generated room mockup requests</p>
    </div>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">DATE</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CUSTOMER</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PRODUCT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PREVIEW</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visualizations as $v)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3" style="font-size:13px; color:rgba(18,18,18,0.55);">{{ $v->created_at->format('M j, Y') }}</td>
                <td class="px-4 py-3">
                    <p style="font-size:14px; font-weight:500; color:#121212;">{{ $v->user->name ?? 'Guest' }}</p>
                    <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $v->user->email ?? '—' }}</p>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $v->product->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($v->generated_image_path)
                    <a href="{{ asset('storage/' . $v->generated_image_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $v->generated_image_path) }}" alt="" class="w-16 h-12 object-cover rounded" style="border:1px solid rgba(18,18,18,0.1);">
                    </a>
                    @else
                    <span style="font-size:12px; color:rgba(18,18,18,0.45);">Processing...</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span style="font-size:11px; font-weight:500; padding:3px 10px; border-radius:20px;
                        {{ $v->status === 'completed' ? 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;' : 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;' }}">
                        {{ ucfirst($v->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-5 py-3 border-t border-stone-100">{{ $visualizations->links() }}</div>
    @if($visualizations->isEmpty())
    <div class="px-6 py-12 text-center">
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No visualization requests yet.</p>
    </div>
    @endif
</div>

@endsection
