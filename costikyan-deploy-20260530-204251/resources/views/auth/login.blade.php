<x-guest-layout>

{{-- Heading --}}
<div class="mb-9">
    <p style="font-size:11px; font-weight:600; letter-spacing:0.12em; color:#E8651A; text-transform:uppercase; margin-bottom:10px;">Welcome back</p>
    <h1 style="font-family:'Lusitana',serif; font-size:30px; font-weight:700; color:#111; line-height:1.2;">Sign in to your account</h1>
    <p class="mt-3" style="font-size:14px; color:#6b7280;">Access your orders, wishlist, and trade portal.</p>
</div>

{{-- Status --}}
@session('status')
<div class="mb-5 flex items-center gap-2.5 px-4 py-3 rounded-md text-sm text-green-700" style="background:#f0fdf4; border:1px solid #bbf7d0;">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ $value }}
</div>
@endsession

{{-- Validation errors --}}
@if($errors->any())
<div class="mb-5 px-4 py-3 rounded-md text-sm text-red-700" style="background:#fef2f2; border:1px solid #fecaca;">
    <p class="font-semibold mb-1">Please fix the following:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    {{-- Email --}}
    <div>
        <label for="email" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
            Email address
        </label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               required autofocus autocomplete="username"
               style="display:block; width:100%; padding:11px 14px; font-size:14px; color:#111; background:#fff;
                      border:1.5px solid {{ $errors->has('email') ? '#f87171' : '#e5e7eb' }};
                      border-radius:6px; transition:border-color 0.15s; outline:none;"
               onfocus="this.style.borderColor='#E8651A'"
               onblur="this.style.borderColor='{{ $errors->has('email') ? '#f87171' : '#e5e7eb' }}'"
               placeholder="you@example.com">
    </div>

    {{-- Password --}}
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" style="font-size:13px; font-weight:500; color:#374151;">Password</label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" style="font-size:12px; color:#E8651A; text-decoration:none;" class="hover:underline">
                Forgot password?
            </a>
            @endif
        </div>
        <div class="relative" x-data="{ show: false }">
            <input id="password" :type="show ? 'text' : 'password'" name="password"
                   required autocomplete="current-password"
                   style="display:block; width:100%; padding:11px 40px 11px 14px; font-size:14px; color:#111; background:#fff;
                          border:1.5px solid {{ $errors->has('password') ? '#f87171' : '#e5e7eb' }};
                          border-radius:6px; transition:border-color 0.15s; outline:none;"
                   onfocus="this.style.borderColor='#E8651A'"
                   onblur="this.style.borderColor='{{ $errors->has('password') ? '#f87171' : '#e5e7eb' }}'"
                   placeholder="••••••••">
            <button type="button" @click="show=!show" tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 4.03-5.373M9.878 9.878A3 3 0 0 0 12 15a3 3 0 0 0 2.122-5.122M3 3l18 18"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Remember me --}}
    <div class="flex items-center gap-2.5">
        <input id="remember_me" type="checkbox" name="remember"
               class="w-4 h-4 rounded" style="accent-color:#E8651A;">
        <label for="remember_me" style="font-size:13px; color:#6b7280; cursor:pointer;">Keep me signed in</label>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full flex items-center justify-center gap-2 py-3 font-semibold text-sm text-white rounded-md transition-all duration-150 hover:opacity-90 active:scale-[0.99]"
            style="background:#111111; font-size:14px; letter-spacing:0.03em;">
        Sign In
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </button>

    {{-- Divider --}}
    <div class="flex items-center gap-3 py-1">
        <div class="flex-1 h-px" style="background:#e5e7eb;"></div>
        <span style="font-size:12px; color:#9ca3af;">or</span>
        <div class="flex-1 h-px" style="background:#e5e7eb;"></div>
    </div>

    {{-- Register link --}}
    <p class="text-center" style="font-size:13px; color:#6b7280;">
        Don't have an account?
        <a href="{{ route('register') }}" style="color:#E8651A; font-weight:600; text-decoration:none;" class="hover:underline">
            Create one free
        </a>
    </p>
</form>

</x-guest-layout>
