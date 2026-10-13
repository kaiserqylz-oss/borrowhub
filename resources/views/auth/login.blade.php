<x-guest-layout>
    <div class="mb-6"><h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h1><p class="mt-1 text-sm text-slate-600">Sign in to manage your listings and borrowing requests.</p></div>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="space-y-5">@csrf
        <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" class="mt-1.5 block min-h-11 w-full rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-1.5" /></div>
        <div><div class="flex items-center justify-between"><x-input-label for="password" :value="__('Password')" />@if(Route::has('password.request'))<a class="text-sm font-medium text-blue-700 hover:text-blue-800" href="{{ route('password.request') }}">Forgot password?</a>@endif</div><x-text-input id="password" class="mt-1.5 block min-h-11 w-full rounded-lg" type="password" name="password" required autocomplete="current-password" /><x-input-error :messages="$errors->get('password')" class="mt-1.5" /></div>
        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600"><input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600" name="remember"><span>{{ __('Remember me') }}</span></label>
        <button class="min-h-11 w-full rounded-lg bg-blue-600 px-4 font-semibold text-white hover:bg-blue-700">Log in</button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">New to BorrowHub? <a class="font-semibold text-blue-700 hover:text-blue-800" href="{{ route('register') }}">Create an account</a></p>
</x-guest-layout>
