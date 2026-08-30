@extends('layouts.site')
@section('title', 'Services')

@section('content')
<section class="relative h-[40vh] min-h-[300px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/services-hero.jpg') }}" alt="Rug services" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>
    <div class="relative z-10 text-center px-4">
        <h1 class="font-serif text-4xl md:text-5xl font-bold text-white">Our Services</h1>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        @foreach([
            ['Custom Design', 'Work with our design team to create a rug made entirely to your specifications — from fiber selection to finishing edges.'],
            ['Rug Cleaning', 'Professional hand-washing and deep cleaning for all rug types, restoring color and softness without damage.'],
            ['Rug Repair & Restoration', 'Skilled restoration of antique and heirloom rugs, including re-weaving, re-fringing, and color correction.'],
            ['White-Glove Installation', 'Expert in-home placement and installation with careful attention to padding, alignment, and furniture arrangement.'],
            ['Rug Appraisal', 'Certified appraisals for insurance, estate, or resale purposes by our team of experienced specialists.'],
            ['Storage', 'Climate-controlled, acid-free storage for seasonal or long-term rug preservation.'],
        ] as [$title, $desc])
        <div class="flex gap-5">
            <div class="w-2 h-2 bg-amber-400 rounded-full flex-shrink-0 mt-2"></div>
            <div>
                <h3 class="font-serif text-xl font-bold mb-2">{{ $title }}</h3>
                <p class="text-sm text-stone-600 leading-relaxed">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-14 text-center">
        <a href="{{ route('contact') }}" class="btn-dark px-10">Enquire About a Service</a>
    </div>
</div>
@endsection
