<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('layouts.pwa-head')

        <title>BorrowHub</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        @include('layouts.pwa-status')
        <main class="min-h-screen flex flex-col items-center justify-center px-4 py-10 sm:px-6">
            <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-3 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white" aria-hidden="true">B</span>
                <span class="text-xl font-bold tracking-tight">Borrow<span class="text-blue-600">Hub</span></span>
            </a>

            <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white px-6 py-7 shadow-sm sm:px-8">
                {{ $slot }}
                <button type="button" data-install-app hidden class="mt-5 min-h-11 w-full rounded-lg border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-800 hover:bg-blue-100">Install BorrowHub</button>
            </div>
            <p class="mt-6 text-center text-sm text-slate-500">Share what you have. Borrow what you need.</p>
        </main>
    </body>
</html>
