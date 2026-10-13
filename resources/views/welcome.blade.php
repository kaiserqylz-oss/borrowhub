<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.pwa-head')
    <title>BorrowHub · Share locally</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center gap-2 text-lg font-bold"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white" aria-hidden="true">B</span>Borrow<span class="-ml-2 text-blue-600">Hub</span></a>
            <div class="flex flex-wrap items-center justify-end gap-1 sm:gap-2">
                <button type="button" data-install-app hidden class="min-h-10 rounded-lg border border-blue-200 bg-blue-50 px-3 text-sm font-semibold text-blue-800 hover:bg-blue-100">Install BorrowHub</button>
                <a href="{{ route('items.browse') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Browse</a>
                @if(Route::has('login'))<a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Log in</a>@endif
                @if(Route::has('register'))<a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Join BorrowHub</a>@endif
            </div>
        </div>
    </header>
    @include('layouts.pwa-status')
    <main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <section class="max-w-3xl"><p class="text-sm font-bold uppercase tracking-widest text-blue-700">A neighborly way to share</p><h1 class="mt-4 text-4xl font-bold tracking-tight text-slate-900 sm:text-6xl">Borrow what you need. Share what you have.</h1><p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">BorrowHub connects people who want to lend useful things with neighbors who need them for a little while.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('items.browse') }}" class="inline-flex min-h-12 items-center rounded-lg bg-blue-600 px-6 font-semibold text-white hover:bg-blue-700">Explore available items</a>@if(Route::has('register'))<a href="{{ route('register') }}" class="inline-flex min-h-12 items-center rounded-lg border border-slate-300 bg-white px-6 font-semibold text-slate-700 hover:bg-slate-50">Create an account</a>@endif</div></section>
        <section class="mt-16 grid gap-4 sm:grid-cols-3"><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="font-semibold">Find useful things</p><p class="mt-2 text-sm leading-6 text-slate-600">Search community listings by name and category.</p></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="font-semibold">Share with neighbors</p><p class="mt-2 text-sm leading-6 text-slate-600">List tools, equipment, and other items you are willing to lend.</p></article><article class="rounded-2xl border border-slate-200 bg-white p-5"><p class="font-semibold">Borrow with confidence</p><p class="mt-2 text-sm leading-6 text-slate-600">Track requests, returns, and genuine transaction reviews.</p></article></section>
    </main>
</body>
</html>
