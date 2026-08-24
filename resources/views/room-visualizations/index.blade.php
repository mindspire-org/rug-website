@extends('layouts.dashboard')
@section('title', 'My Visualizations')

@section('dashboard-content')
<div x-data="{ preview: null }">
    <h1 class="font-serif text-xl lg:text-2xl font-bold mb-2">My Room Visualizations</h1>
    <p class="text-sm text-stone-500 mb-6 lg:mb-8">AI-generated previews of rugs placed in your own rooms.</p>

    @if($visualizations->isEmpty())
        <div class="border border-stone-200 p-10 text-center rounded-lg">
            <svg class="w-10 h-10 mx-auto mb-3 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
            </svg>
            <p class="text-sm text-stone-500 mb-4">You haven't created any room visualizations yet.</p>
            <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center text-white px-5 hover:opacity-90"
               style="background:#E8651A; height:40px; border-radius:3px; font-size:13px; font-weight:500;">Browse rugs</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
            @foreach($visualizations as $viz)
            <div class="border border-stone-200 rounded-lg overflow-hidden bg-white">
                <div class="relative aspect-square bg-stone-100">
                    @if($viz->status === 'completed' && $viz->generated_image_url)
                        <img src="{{ $viz->generated_image_url }}" alt="Visualization"
                             class="w-full h-full object-cover cursor-pointer"
                             @click="preview = '{{ $viz->generated_image_url }}'">
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-semibold text-white" style="background:#15803d;">Ready</span>
                    @elseif($viz->status === 'processing')
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold" style="background:#fef9c3; color:#854d0e;">Processing…</span>
                        </div>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold mb-2" style="background:#fee2e2; color:#b91c1c;">Failed</span>
                            <p class="text-[11px] text-stone-400">{{ \Illuminate\Support\Str::limit($viz->error_message, 80) }}</p>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium text-stone-900 truncate">{{ $viz->product->name ?? 'Rug' }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-[11px] text-stone-400">{{ $viz->created_at->format('M j, Y') }}</p>
                        @if($viz->status === 'completed' && $viz->generated_image_url)
                        <a href="{{ route('room.visualization.download', $viz) }}"
                           class="text-[11px] font-medium" style="color:#E8651A;">Download</a>
                        @elseif($viz->product)
                        <a href="{{ route('shop.show', $viz->product->slug ?? $viz->product->id) }}"
                           class="text-[11px] font-medium" style="color:#E8651A;">Try again</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $visualizations->links() }}
        </div>
    @endif

    {{-- Lightbox --}}
    <div x-show="preview" x-cloak @click="preview = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.8);">
        <img :src="preview" alt="Visualization" class="max-w-full rounded-lg shadow-2xl" style="max-height:90vh;">
    </div>
</div>
@endsection
