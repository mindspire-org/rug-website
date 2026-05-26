@extends('layouts.site')
@section('title', 'Our Story — Est. 1886')

@section('content')

{{-- ── 1. HERO ── --}}
<section class="relative overflow-hidden flex items-end" style="height:55vh; min-height:380px;">
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" alt="Costikyan workshop"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.25) 100%);"></div>
    </div>
    <div class="relative z-10 px-8 pb-12 max-w-[1100px] mx-auto w-full">
        <h1 style="font-family:'Lusitana',serif; font-size:clamp(32px,5vw,58px); font-weight:700; color:#fff; line-height:1.15;">
            Crafted for<br>Every Space
        </h1>
    </div>
</section>

{{-- ── 2. QUOTE ── --}}
<section class="bg-white py-16">
    <div class="max-w-[760px] mx-auto px-6 text-center">
        <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#8B6914; text-transform:uppercase;
                     background:#F9F0DC; border-radius:20px; padding:4px 14px; display:inline-block;" class="mb-8">
            COSTIKYAN
        </span>
        <blockquote style="font-family:'Lusitana',serif; font-size:clamp(18px,2.2vw,26px); font-style:italic; color:#121212; line-height:1.6;" class="mt-6">
            "Since 1886, Costikyan Custom Carpet has created rugs designed for specific spaces and the people who live in them. From the earliest days of custom rug making in America, our work has been guided by a simple belief: a rug should be made to belong."
        </blockquote>
    </div>
</section>

{{-- ── 3. OUR HISTORY TIMELINE ── --}}
<section class="bg-white pb-16">
    <div class="max-w-[1100px] mx-auto px-6">
        <h2 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;" class="mb-8">Our History</h2>

        {{-- Timeline scroll --}}
        <div class="relative" x-data="{ active: 0 }">
            {{-- Year tabs --}}
            <div class="flex gap-0 overflow-x-auto mb-8 border-b border-stone-200">
                @php
                $timeline = [
                    ['year'=>'1886','title'=>'Origins in Constantinople','desc'=>'Harutun Costikyan established his first workshop in Constantinople, specialising in hand-knotted Persian and Caucasian rugs. His mastery and commitment to quality earned international recognition.'],
                    ['year'=>'1902','title'=>'Arriving in America','desc'=>'After immigrating to New York, Costikyan expanded his business to serve the growing American market, bringing Old World craftsmanship to a new continent.'],
                    ['year'=>'1940','title'=>'Mid-Century Growth','desc'=>'Through the post-war boom, Costikyan supplied rugs to landmark hotels, cultural institutions, and the residences of prominent American families.'],
                    ['year'=>'1975','title'=>'Design Evolution','desc'=>'The company embraced contemporary design movements while maintaining its handcrafted traditions, collaborating with leading interior designers and architects.'],
                    ['year'=>'2000','title'=>'Global Reach','desc'=>'Entering the 21st century, Costikyan expanded globally, establishing partnerships with master weavers across Nepal, India, and Morocco.'],
                    ['year'=>'2020','title'=>'Custom by Design','desc'=>'Today, every rug we make starts with a conversation. Our custom rug programme — from blank canvas to finished piece — has become the cornerstone of our business.'],
                ];
                @endphp
                @foreach($timeline as $i => $item)
                <button @click="active = {{ $i }}"
                        :class="{{ $i }} === active ? 'border-b-2 border-[#121212] text-[#121212]' : 'text-stone-400'"
                        class="flex-shrink-0 px-5 py-3 transition-colors"
                        style="font-size:13px; font-weight:500; white-space:nowrap;">
                    {{ $item['year'] }}
                </button>
                @endforeach
            </div>

            {{-- Timeline content panels --}}
            @foreach($timeline as $i => $item)
            <div x-show="active === {{ $i }}" x-cloak class="grid grid-cols-1 md:grid-cols-[260px_1fr] gap-8 items-start">
                <div class="overflow-hidden" style="aspect-ratio:1/1; border-radius:3px; background:#f0ece6;">
                    <img src="{{ asset('images/cover.jpg') }}" alt="{{ $item['title'] }}"
                         class="w-full h-full object-cover">
                </div>
                <div>
                    <p style="font-size:12px; color:rgba(18,18,18,0.45); letter-spacing:0.08em;" class="mb-2">{{ $item['year'] }}</p>
                    <h3 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212; line-height:1.3;" class="mb-4">{{ $item['title'] }}</h3>
                    <p style="font-size:14px; color:rgba(18,18,18,0.7); line-height:1.75;">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach

            {{-- Read More button --}}
            <div class="text-center mt-10">
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center justify-center border"
                   style="border-color:rgba(18,18,18,0.25); border-radius:3px; padding:10px 28px;
                          font-family:'Lusitana',serif; font-size:14px; color:#121212;">
                    Read Our Story
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ── 4. FAQ ── --}}
<section class="bg-white py-16" style="border-top:1px solid rgba(18,18,18,0.08);">
    <div class="max-w-[1100px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-12">
            <div>
                <h2 style="font-family:'Lusitana',serif; font-size:26px; font-weight:700; color:#121212; line-height:1.3;">
                    Everything You Need To Know
                </h2>
                <p style="font-size:13px; color:rgba(18,18,18,0.6); line-height:1.7; margin-top:12px;">
                    Answers to the most common questions about our rugs, process, and services.
                </p>
            </div>

            <div x-data="{ open: null }" class="space-y-0">
                @php
                $faqs = [
                    ['cat'=>'Buying a Rug','items'=>[
                        ['q'=>'How do I shop for a rug?','a'=>'Browse our collection online or visit one of our showrooms. Use filters to narrow by size, material, style, or construction.'],
                        ['q'=>'Which rug sizes are available to consider?','a'=>'Standard sizes range from 3×5 to 12×15. We also offer fully custom dimensions.'],
                        ['q'=>'Can I order a rug?','a'=>'Yes — all rugs can be ordered online or through our design team.'],
                        ['q'=>'Why shop your own custom rug for your home?','a'=>'A custom rug ensures a perfect fit, palette match, and material selection for your specific space.'],
                        ['q'=>'Can I request a change to my area rug?','a'=>'Yes, within 48 hours of placing your order you may request modifications.'],
                        ['q'=>'How does rug delivery work?','a'=>'We offer white-glove delivery, standard UPS shipping, and warehouse pickup.'],
                        ['q'=>'Can I choose a white-glove delivery?','a'=>'Yes. White-glove includes installation and placement by our professional team.'],
                        ['q'=>'What timeline can I expect on my rug?','a'=>'In-stock rugs ship within 2 weeks. Custom orders take 8–12 weeks.'],
                        ['q'=>'Will I be asked about a custom rug?','a'=>'Our design team will walk you through options during a free consultation.'],
                        ['q'=>'Can I change my shipping after setting?','a'=>'Contact us within 24 hours of order placement to change shipping method.'],
                        ['q'=>'How do I return an order?','a'=>'In-stock items may be returned within 30 days. Custom orders are final sale.'],
                    ]],
                    ['cat'=>'Sizing Your Rug','items'=>[
                        ['q'=>'How do I determine the right size for a rug?','a'=>'Measure your furniture grouping and allow 18–24 inches on each side.'],
                        ['q'=>'What size rug should I buy?','a'=>'For living rooms, an 8×10 or 9×12 works well under a standard sofa grouping.'],
                        ['q'=>'How do I choose the right pad?','a'=>'Choose a pad 1–2 inches smaller than your rug on all sides.'],
                        ['q'=>'Can I place my rug on wall-to-wall?','a'=>'Yes, a rug over carpet defines a seating area and adds texture.'],
                        ['q'=>'What are rug padding?','a'=>'Rug pads prevent slipping, protect floors, and extend the life of your rug.'],
                        ['q'=>'How are delivery pricing to meet?','a'=>'Delivery costs vary by location. Enter your ZIP code at checkout for pricing.'],
                    ]],
                    ['cat'=>'Custom Rugs','items'=>[
                        ['q'=>'What should I do to start designing my rug?','a'=>'Submit a project inquiry with your dimensions, style references, and budget.'],
                        ['q'=>'Who should I call when offering the rug designs?','a'=>'Our design team is available at 800-247-7847 or via our contact form.'],
                        ['q'=>'How exactly do I commission an area rug?','a'=>'Complete the Weave Your Dream Rug form and we will schedule a consultation.'],
                        ['q'=>'Can I review my custom rug after ordering?','a'=>'We provide sample approvals before full production begins.'],
                        ['q'=>'What offers do you provide for commission designs?','a'=>'We offer full-custom, semi-custom, and size-only customization options.'],
                    ]],
                ];
                $idx = 0;
                @endphp

                @foreach($faqs as $section)
                <div class="mb-8">
                    <p style="font-size:13px; font-weight:700; color:#121212; letter-spacing:0.04em; text-transform:uppercase;"
                       class="mb-3">{{ $section['cat'] }}</p>
                    @foreach($section['items'] as $item)
                    @php $faqId = $idx++; @endphp
                    <div style="border-top:1px solid rgba(18,18,18,0.1);">
                        <button @click="open === {{ $faqId }} ? open = null : open = {{ $faqId }}"
                                class="w-full flex items-center justify-between py-3 text-left"
                                style="font-size:14px; color:#121212;">
                            <span>{{ $item['q'] }}</span>
                            <svg :class="open === {{ $faqId }} ? 'rotate-180' : ''"
                                 class="flex-shrink-0 ml-4 transition-transform"
                                 width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $faqId }}" x-cloak
                             style="font-size:13px; color:rgba(18,18,18,0.65); line-height:1.7; padding-bottom:12px;">
                            {{ $item['a'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@endsection
