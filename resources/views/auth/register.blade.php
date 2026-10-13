<x-guest-layout>
    <div class="mb-6"><h1 class="text-2xl font-bold tracking-tight text-slate-900">Create your account</h1><p class="mt-1 text-sm text-slate-600">Join your community and start sharing useful items.</p></div>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">@csrf
        <div><x-input-label for="name" :value="__('Name')" /><x-text-input id="name" class="mt-1.5 block min-h-11 w-full rounded-lg" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" /><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
        <div><x-input-label for="email" :value="__('Email address')" /><x-text-input id="email" class="mt-1.5 block min-h-11 w-full rounded-lg" type="email" name="email" :value="old('email')" required autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-1" /></div>
        <div><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" class="mt-1.5 block min-h-11 w-full rounded-lg" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-1" /></div>
        <div><x-input-label for="password_confirmation" :value="__('Confirm password')" /><x-text-input id="password_confirmation" class="mt-1.5 block min-h-11 w-full rounded-lg" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" /></div>
        <button class="min-h-11 w-full rounded-lg bg-blue-600 px-4 font-semibold text-white hover:bg-blue-700">Create account</button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">Already registered? <a class="font-semibold text-blue-700 hover:text-blue-800" href="{{ route('login') }}">Log in</a></p>
</x-guest-layout>
