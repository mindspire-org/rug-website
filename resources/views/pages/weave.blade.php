@extends('layouts.site')
@section('title', 'Weave Your Dream Rug From Scratch')

@section('content')
<section class="relative h-[45vh] min-h-[320px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/weave-hero.jpg') }}" alt="Custom rug weaving" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>
    <div class="relative z-10 text-center px-4">
        <h1 class="font-serif text-4xl md:text-5xl font-bold text-white mb-3">Weave Your Dream Rug</h1>
        <p class="text-stone-200 text-sm md:text-base">Start from a blank canvas. We'll bring your vision to life.</p>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
        <div>
            <h2 class="font-serif text-2xl font-bold mb-4">How It Works</h2>
            <div class="space-y-6">
                @foreach([['1', 'Share Your Vision', 'Tell us about your space, style, dimensions, and color palette. The more detail, the better.'], ['2', 'Design Collaboration', 'Our team works with you to translate your ideas into a detailed design brief and sample swatches.'], ['3', 'Expert Craftsmanship', 'Skilled artisans weave your rug by hand using only the finest materials.'], ['4', 'White-Glove Delivery', 'Your custom rug is delivered and installed with care, exactly where you want it.']] as [$num, $title, $desc])
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-amber-400 flex items-center justify-center flex-shrink-0 font-bold text-stone-900 text-sm">{{ $num }}</div>
                    <div>
                        <h3 class="font-semibold text-stone-900 mb-1">{{ $title }}</h3>
                        <p class="text-sm text-stone-600">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="font-serif text-2xl font-bold mb-6">Start Your Request</h2>
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 mb-6 text-sm">{{ session('success') }}</div>
            @endif
            <form action="{{ route('weave.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Phone (optional)</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Style / Inspiration</label>
                    <input type="text" name="style" value="{{ old('style') }}" placeholder="e.g. Modern geometric, Persian floral…" class="form-input">
                </div>
                <div>
                    <label class="form-label">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="e.g. 9' × 12'" class="form-input">
                </div>
                <div>
                    <label class="form-label">Project Description</label>
                    <textarea name="description" rows="5" class="form-input resize-none" placeholder="Tell us about your space, color palette, and any specific requirements…" required>{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn-dark w-full justify-center py-3.5">Submit Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
