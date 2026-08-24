@extends('layouts.site')
@section('title', 'Our Story — Est. 1886')

@section('content')

{{-- ══════════════════════════════════════════
     1. HERO
  ══════════════════════════════════════════ --}}
<section class="relative overflow-hidden flex items-end" style="height:55vh; min-height:380px;">
    <div class="absolute inset-0">
        <img src="{{ asset('images/about-hero-poster.png') }}" alt="Costikyan workshop"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.25) 100%);"></div>
    </div>
    <div class="relative z-10 px-8 pb-12 max-w-[1100px] mx-auto w-full">
        <h1 style="font-family:'Lusitana',serif; font-size:clamp(32px,5vw,58px); font-weight:700; color:#fff; line-height:1.15;">
            Crafted for<br>Every Space
        </h1>
    </div>
</section>

{{-- ══════════════════════════════════════════
     2. QUOTE
  ══════════════════════════════════════════ --}}
<section class="bg-white pt-16 pb-12">
    <div class="max-w-[760px] mx-auto px-6 text-center">
        <span style="font-size:9px; font-weight:600; letter-spacing:0.15em; color:#8B6914; text-transform:uppercase;
                     border:1px solid #e5d9b6; border-radius:20px; padding:4px 16px; display:inline-block;"
              class="mb-6">
            ESTABLISHED 1886
        </span>
        <blockquote style="font-family:'Lusitana',serif; font-size:clamp(18px,2.2vw,26px); font-style:italic; color:#121212; line-height:1.6;" class="mt-6">
            "Since 1886, Costikyan Custom Carpet has created rugs designed for specific spaces and the people who live in them. From the earliest days of custom rug making in America, our work has been guided by a simple belief: a rug should be made to belong."
        </blockquote>
    </div>
</section>

{{-- ══════════════════════════════════════════
     3. OUR HISTORY — Dark timeline section
  ══════════════════════════════════════════ --}}
<section style="background:#111111;" x-data="{ era: 0 }">
    <div class="max-w-[1100px] mx-auto px-6 py-16">
        <h2 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#fff;" class="mb-10">Our History</h2>

        @php
        $eras = [
            ['year'=>'1880','img_a'=>'about-1880-a.png','img_b'=>'about-1880-b.png','title'=>'Origins in Constantinople','desc'=>'The Costikyan family\'s roots trace back to Constantinople, where generations were deeply involved in the traditional rug trade, developing expertise passed down through family lineage.'],
            ['year'=>'1900','img_a'=>'about-1900-a.png','img_b'=>'about-1900-b.png','fit'=>'contain','title'=>'Arriving in America','desc'=>'After immigrating to New York, Costikyan expanded his business to serve the growing American market, bringing Old World craftsmanship to a new continent.'],
            ['year'=>'1940','img_a'=>'about-1940-a.png','img_b'=>'about-1940-b.png','title'=>'Mid-Century Growth','desc'=>'Through the post-war boom, Costikyan supplied rugs to landmark hotels, cultural institutions, and the residences of prominent American families.'],
            ['year'=>'1980','img_a'=>'about-1980-a.png','img_b'=>'about-1980-b.png','title'=>'Design Evolution','desc'=>'The company embraced contemporary design movements while maintaining its handcrafted traditions, collaborating with leading interior designers and architects.'],
            ['year'=>'2000','img_a'=>'about-2000-a.png','img_b'=>'about-2000-b.png','title'=>'Global Reach','desc'=>'Entering the 21st century, Costikyan expanded globally, establishing partnerships with master weavers across Nepal, India, and Morocco.'],
            ['year'=>'2025','img_a'=>'about-2025-a.png','img_b'=>'about-2025-b.png','title'=>'Custom by Design','desc'=>'Today, every rug we make starts with a conversation. Our custom rug programme — from blank canvas to finished piece — has become the cornerstone of our business.'],
        ];
        @endphp

        {{-- Horizontal year timeline --}}
        <div class="relative mb-10">
            <div class="flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.15); padding-bottom:12px;">
                @foreach($eras as $i => $e)
                <button @click="era = {{ $i }}"
                        :class="era === {{ $i }} ? 'text-white' : 'text-stone-500 hover:text-stone-300'"
                        class="relative text-center transition-colors"
                        style="font-size:14px; font-weight:500;">
                    <span :class="era === {{ $i }} ? 'text-amber-400' : ''">{{ $e['year'] }}</span>
                    <div x-show="era === {{ $i }}"
                         class="absolute left-1/2 -translate-x-1/2 -bottom-[13px] w-2 h-2 rounded-full bg-amber-400"></div>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Era content — two images side by side --}}
        @foreach($eras as $i => $e)
        <div x-show="era === {{ $i }}" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left image (portrait / history) --}}
                <div class="overflow-hidden" style="border-radius:3px; aspect-ratio:4/5; background:#1a1a1a;">
                    <img src="{{ asset('images/' . $e['img_a']) }}" alt="{{ $e['title'] }}"
                         class="w-full h-full opacity-80 hover:opacity-100 transition-opacity duration-500"
                         style="object-fit:{{ $e['fit'] ?? 'cover' }}; object-position:center;">
                </div>
                {{-- Right image (rug / craft) --}}
                <div class="overflow-hidden" style="border-radius:3px; aspect-ratio:4/5; background:#1a1a1a;">
                    <img src="{{ asset('images/' . $e['img_b']) }}" alt="Rug craft"
                         class="w-full h-full opacity-80 hover:opacity-100 transition-opacity duration-500"
                         style="object-fit:{{ $e['fit'] ?? 'cover' }}; object-position:center;">
                </div>
            </div>
            <div class="mt-6">
                <p style="font-size:12px; color:#E8651A; letter-spacing:0.08em; font-weight:600;" class="mb-2">{{ $e['year'] }}</p>
                <h3 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#fff; line-height:1.3;" class="mb-3">{{ $e['title'] }}</h3>
                <p style="font-size:14px; color:rgba(255,255,255,0.6); line-height:1.75; max-width:640px;">{{ $e['desc'] }}</p>
            </div>
        </div>
        @endforeach

        {{-- Expert Consultation button --}}
        <div class="text-center mt-10">
            <a href="{{ route('contact') }}"
               class="inline-flex items-center justify-center"
               style="background:#fff; color:#121212; border-radius:3px; padding:12px 32px;
                      font-size:13px; font-weight:500; letter-spacing:0.02em; text-decoration:none;">
                Expert Consultation
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     4. FAQ — Everything You Need To Know
  ══════════════════════════════════════════ --}}
<section class="bg-white" style="border-top:1px solid rgba(18,18,18,0.08);">
    <div class="max-w-[1100px] mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-12">
            {{-- Left: heading + description --}}
            <div>
                <h2 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212; line-height:1.2;">
                    Everything You Need<br>To Know
                </h2>
                <p style="font-size:13px; color:rgba(18,18,18,0.6); line-height:1.7; margin-top:12px;">
                    Discover the nuances of our craftsmanship, the care of heritage floors, and our personalized concierge services.
                </p>
            </div>

            {{-- Right: accordion FAQ --}}
            <div x-data="{ open: null }">
                @php
                $faqs = [
                    ['cat'=>'Samples','items'=>[
                        ['q'=>'How do rug samples work?','a'=>'Request up to 5 complimentary samples. Each includes a swatch of your chosen material and weave so you can experience texture and colour in your own space before committing.'],
                        ['q'=>'What shipping options are available for samples?','a'=>'Samples ship via standard USPS within 3–5 business days. Expedited options are available at checkout.'],
                    ]],
                    ['cat'=>'Order & Delivery Info','items'=>[
                        ['q'=>'Where do you ship?','a'=>'We ship throughout the United States and to select international destinations. Contact us for a custom shipping quote outside our standard zones.'],
                        ['q'=>'What shipping options are available?','a'=>'Standard ground, white-glove delivery with in-home placement, and warehouse pickup for local clients.'],
                        ['q'=>'Can I change or cancel my order after placing it?','a'=>'Orders may be modified or cancelled within 24 hours. Custom orders enter production immediately and cannot be cancelled once weaving begins.'],
                        ['q'=>'How do I reschedule my delivery?','a'=>'Contact our logistics team at least 48 hours before your scheduled delivery window.'],
                        ['q'=>'Can my rug be held for later delivery?','a'=>'Yes. We offer complimentary storage for up to 30 days if your project timeline requires delayed delivery.'],
                        ['q'=>'What does White Glove delivery include?','a'=>'Our white-glove service includes unwrapping, placement, and debris removal. Furniture moving is not included but can be arranged separately.'],
                        ['q'=>'What does haul-away service include?','a'=>'Haul-away removes your old rug and padding for recycling or donation. Fee varies by rug size and location.'],
                        ['q'=>'Will furniture be moved onto my rug?','a'=>'Furniture placement onto the rug is included with white-glove delivery. Please clear the room of small items before our team arrives.'],
                        ['q'=>'Can I change my shipping option after ordering?','a'=>'Shipping upgrades can be requested within 24 hours of order confirmation by contacting customer service.'],
                        ['q'=>'How do I submit my COP','a'=>'Certificate of Product (COP) documentation is generated automatically and emailed with your shipping confirmation.'],
                    ]],
                    ['cat'=>'Picking Your Rug','items'=>[
                        ['q'=>'How does the custom rug process work?','a'=>'Begin with a consultation to discuss dimensions, materials, patterns, and timeline. We then create detailed renderings for your approval before production begins.'],
                        ['q'=>'What materials should I choose?','a'=>'Wool offers durability and softness. Silk adds sheen and luxury. Cotton is lightweight and easy to clean. Our design team guides you based on traffic and aesthetics.'],
                        ['q'=>'How does pattern scaling work?','a'=>'Patterns are digitally mapped to your exact dimensions. We provide a scale proof showing motif repetition before weaving.'],
                        ['q'=>'How do I choose the right rug size?','a'=>'Allow 18–24 inches of exposed floor beyond furniture groupings. Our room-planning tool helps visualize proportions.'],
                        ['q'=>'Can I create a custom-shaped rug?','a'=>'Yes. We produce round, oval, and irregular shapes. Simply provide dimensions or a template.'],
                        ['q'=>'What is rug binding?','a'=>'Binding finishes raw edges with a stitched fabric tape, protecting the rug\'s perimeter and extending its life.'],
                        ['q'=>'How much does rug binding cost?','a'=>'Binding is included with all custom orders. For existing rugs, pricing starts at $4 per linear foot.'],
                    ]],
                    ['cat'=>'Care & Service','items'=>[
                        ['q'=>'What should I do if my rug arrives damaged?','a'=>'Photograph the damage and contact us within 48 hours. We will arrange a replacement or repair at no cost.'],
                        ['q'=>'What should I do with my old rug?','a'=>'We offer haul-away and recycling services. Alternatively, donate through our partner network for a tax-deductible receipt.'],
                    ]],
                    ['cat'=>'Rug Management','items'=>[
                        ['q'=>'How should I clean my rug?','a'=>'Vacuum weekly without a beater bar. Spot-clean with mild detergent and water. Professional cleaning is recommended every 12–18 months.'],
                        ['q'=>'Can I protect my rug from stains and wear?','a'=>'Apply a fibre protector treatment and rotate the rug 180° every 6 months to distribute wear evenly.'],
                        ['q'=>'Do you offer rug cleaning or restoration services?','a'=>'Yes. Our certified restoration team handles cleaning, reweaving, fringe repair, and colour restoration for heirloom pieces.'],
                    ]],
                ];
                $faqIdx = 0;
                @endphp

                @foreach($faqs as $section)
                <div class="mb-8">
                    <p style="font-size:13px; font-weight:700; color:#121212; letter-spacing:0.04em; text-transform:uppercase;"
                       class="mb-2">{{ $section['cat'] }}</p>
                    @foreach($section['items'] as $item)
                    @php $faqId = $faqIdx++; @endphp
                    <div style="border-top:1px solid rgba(18,18,18,0.1);">
                        <button @click="open === {{ $faqId }} ? open = null : open = {{ $faqId }}"
                                class="w-full flex items-center justify-between py-3.5 text-left group"
                                style="font-size:14px; color:#121212;">
                            <span>{{ $item['q'] }}</span>
                            <svg :class="open === {{ $faqId }} ? 'rotate-180' : ''"
                                 class="flex-shrink-0 ml-4 transition-transform duration-200"
                                 width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $faqId }}" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="font-size:13px; color:rgba(18,18,18,0.65); line-height:1.7; padding-bottom:14px;">
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
