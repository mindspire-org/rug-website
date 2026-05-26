@extends('layouts.site')
@section('title', 'Contact Us')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h1 class="section-title">Get in Touch</h1>
        <p class="section-subtitle mt-3">Our team typically responds within one business day.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
        {{-- Form --}}
        <div>
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 mb-6 text-sm">{{ session('success') }}</div>
            @endif
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Message</label>
                    <textarea name="message" rows="5" class="form-input resize-none" required>{{ old('message') }}</textarea>
                    @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-dark w-full justify-center py-3.5">Send Message</button>
            </form>
        </div>

        {{-- Contact info --}}
        <div class="space-y-8">
            <div>
                <h3 class="font-serif text-xl font-bold mb-4">Visit Our Showroom</h3>
                <p class="text-sm text-stone-600 leading-relaxed">979 Third Avenue, Suite 300<br>New York, NY 10022</p>
            </div>
            <div>
                <h3 class="font-serif text-xl font-bold mb-4">Call or Email</h3>
                <p class="text-sm text-stone-600">Toll Free: <a href="tel:8002477847" class="text-stone-900 hover:underline">800-247-7847</a></p>
                <p class="text-sm text-stone-600 mt-1">Email: <a href="mailto:info@costikyancustomcarpet.com" class="text-stone-900 hover:underline">info@costikyancustomcarpet.com</a></p>
            </div>
            <div>
                <h3 class="font-serif text-xl font-bold mb-4">Showroom Hours</h3>
                <div class="space-y-1 text-sm text-stone-600">
                    <p>Monday – Friday: 9:00am – 5:30pm</p>
                    <p>Saturday: By appointment</p>
                    <p>Sunday: Closed</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
