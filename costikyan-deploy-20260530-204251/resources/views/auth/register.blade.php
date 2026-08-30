<x-guest-layout>

{{-- Heading --}}
<div class="mb-7">
    <p style="font-size:11px; font-weight:600; letter-spacing:0.12em; color:#E8651A; text-transform:uppercase; margin-bottom:10px;">New account</p>
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#111; line-height:1.2;">Create your account</h1>
    <p class="mt-2" style="font-size:13px; color:#6b7280;">Join thousands of customers who trust Costikyan.</p>
</div>

{{-- Validation errors --}}
@if($errors->any())
<div class="mb-5 px-4 py-3 rounded-md text-sm text-red-700" style="background:#fef2f2; border:1px solid #fecaca;">
    <p class="font-semibold mb-1">Please fix the following:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4"
      x-data="{ accountType: '{{ old('account_type','customer') }}', showPass: false, showConfirm: false }">
    @csrf

    {{-- Account type selector --}}
    <div>
        <p style="font-size:13px; font-weight:500; color:#374151; margin-bottom:8px;">Account type</p>
        <div class="grid grid-cols-2 gap-3">
            <label class="cursor-pointer" @click="accountType='customer'">
                <input type="radio" name="account_type" value="customer" x-model="accountType" class="sr-only">
                <div class="flex items-center gap-2.5 p-3 rounded-md border-2 transition-all duration-150"
                     :style="accountType==='customer' ? 'border-color:#111; background:#f9f9f9;' : 'border-color:#e5e7eb; background:#fff;'">
                    <svg class="w-4 h-4 flex-shrink-0" :style="accountType==='customer' ? 'color:#111' : 'color:#9ca3af'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                    </svg>
                    <div>
                        <p style="font-size:12px; font-weight:600;" :style="accountType==='customer' ? 'color:#111' : 'color:#6b7280'">Customer</p>
                        <p style="font-size:10px; color:#9ca3af;">Shop & order</p>
                    </div>
                </div>
            </label>
            <label class="cursor-pointer" @click="accountType='trade'">
                <input type="radio" name="account_type" value="trade" x-model="accountType" class="sr-only">
                <div class="flex items-center gap-2.5 p-3 rounded-md border-2 transition-all duration-150"
                     :style="accountType==='trade' ? 'border-color:#E8651A; background:#fff8f5;' : 'border-color:#e5e7eb; background:#fff;'">
                    <svg class="w-4 h-4 flex-shrink-0" :style="accountType==='trade' ? 'color:#E8651A' : 'color:#9ca3af'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
                    </svg>
                    <div>
                        <p style="font-size:12px; font-weight:600;" :style="accountType==='trade' ? 'color:#E8651A' : 'color:#6b7280'">Trade</p>
                        <p style="font-size:10px; color:#9ca3af;">Designer / B2B</p>
                    </div>
                </div>
            </label>
        </div>
    </div>

    {{-- Name --}}
    <div>
        <label for="name" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Full name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               required autofocus autocomplete="name"
               style="display:block; width:100%; padding:11px 14px; font-size:14px; color:#111; background:#fff;
                      border:1.5px solid {{ $errors->has('name') ? '#f87171' : '#e5e7eb' }};
                      border-radius:6px; transition:border-color 0.15s; outline:none;"
               onfocus="this.style.borderColor='#E8651A'"
               onblur="this.style.borderColor='{{ $errors->has('name') ? '#f87171' : '#e5e7eb' }}'"
               placeholder="Jane Smith">
    </div>

    {{-- Email --}}
    <div>
        <label for="email" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Email address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               required autocomplete="username"
               style="display:block; width:100%; padding:11px 14px; font-size:14px; color:#111; background:#fff;
                      border:1.5px solid {{ $errors->has('email') ? '#f87171' : '#e5e7eb' }};
                      border-radius:6px; transition:border-color 0.15s; outline:none;"
               onfocus="this.style.borderColor='#E8651A'"
               onblur="this.style.borderColor='{{ $errors->has('email') ? '#f87171' : '#e5e7eb' }}'"
               placeholder="you@example.com">
    </div>

    {{-- Password --}}
    <div>
        <label for="password" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Password</label>
        <div class="relative">
            <input id="password" :type="showPass ? 'text' : 'password'" name="password"
                   required autocomplete="new-password"
                   style="display:block; width:100%; padding:11px 40px 11px 14px; font-size:14px; color:#111; background:#fff;
                          border:1.5px solid {{ $errors->has('password') ? '#f87171' : '#e5e7eb' }};
                          border-radius:6px; transition:border-color 0.15s; outline:none;"
                   onfocus="this.style.borderColor='#E8651A'"
                   onblur="this.style.borderColor='{{ $errors->has('password') ? '#f87171' : '#e5e7eb' }}'"
                   placeholder="Min. 8 characters">
            <button type="button" @click="showPass=!showPass" tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 4.03-5.373M9.878 9.878A3 3 0 0 0 12 15a3 3 0 0 0 2.122-5.122M3 3l18 18"/></svg>
            </button>
        </div>
    </div>

    {{-- Confirm Password --}}
    <div>
        <label for="password_confirmation" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Confirm password</label>
        <div class="relative">
            <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                   required autocomplete="new-password"
                   style="display:block; width:100%; padding:11px 40px 11px 14px; font-size:14px; color:#111; background:#fff;
                          border:1.5px solid #e5e7eb; border-radius:6px; transition:border-color 0.15s; outline:none;"
                   onfocus="this.style.borderColor='#E8651A'"
                   onblur="this.style.borderColor='#e5e7eb'"
                   placeholder="Repeat password">
            <button type="button" @click="showConfirm=!showConfirm" tabindex="-1"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 4.03-5.373M9.878 9.878A3 3 0 0 0 12 15a3 3 0 0 0 2.122-5.122M3 3l18 18"/></svg>
            </button>
        </div>
    </div>

    {{-- Terms --}}
    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
    <div class="flex items-start gap-2.5">
        <input type="checkbox" name="terms" id="terms" required
               class="mt-0.5 w-4 h-4 rounded flex-shrink-0" style="accent-color:#E8651A;">
        <label for="terms" style="font-size:12px; color:#6b7280; line-height:1.5;">
            I agree to the
            <a href="{{ route('terms.show') }}" target="_blank" style="color:#E8651A; text-decoration:none;" class="hover:underline">Terms of Service</a>
            and
            <a href="{{ route('policy.show') }}" target="_blank" style="color:#E8651A; text-decoration:none;" class="hover:underline">Privacy Policy</a>
        </label>
    </div>
    @endif

    {{-- Submit --}}
    <button type="submit"
            class="w-full flex items-center justify-center gap-2 py-3 font-semibold text-white rounded-md transition-all duration-150 hover:opacity-90 active:scale-[0.99]"
            :style="accountType==='trade'
                ? 'background:#E8651A; font-size:14px; letter-spacing:0.03em;'
                : 'background:#111111; font-size:14px; letter-spacing:0.03em;'">
        <span x-text="accountType==='trade' ? 'Create Trade Account' : 'Create Account'"></span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </button>

    {{-- Sign in link --}}
    <p class="text-center" style="font-size:13px; color:#6b7280;">
        Already have an account?
        <a href="{{ route('login') }}" style="color:#E8651A; font-weight:600; text-decoration:none;" class="hover:underline">Sign in</a>
    </p>
</form>

</x-guest-layout>
