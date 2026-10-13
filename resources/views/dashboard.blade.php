<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Your community marketplace</p><h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h1></div><a href="{{ route('items.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 font-semibold text-slate-700 hover:bg-slate-50">List an item</a></div></x-slot>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-2xl bg-blue-700 px-6 py-8 text-white sm:px-10 sm:py-10">
            <div class="max-w-2xl"><p class="text-sm font-semibold text-blue-100">Borrow locally. Share generously.</p><h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Find something useful nearby.</h2><p class="mt-3 max-w-xl leading-7 text-blue-100">Explore items shared by your community, or list something you are happy to lend.</p>
                <form action="{{ route('items.browse') }}" method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <label for="dashboard-search" class="sr-only">Search items</label><input id="dashboard-search" name="search" type="search" placeholder="Search tools, books, equipment…" class="min-h-12 min-w-0 flex-1 rounded-lg border-0 bg-white px-4 text-slate-900 placeholder:text-slate-500 focus:ring-2 focus:ring-blue-300">
                    <button class="min-h-12 rounded-lg bg-slate-900 px-6 font-semibold text-white hover:bg-slate-800">Search items</button>
                </form>
            </div>
        </section>
        <section class="mt-10">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3"><div><h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Available items</h2><p class="mt-1 text-sm text-slate-600">Listings shared by people across BorrowHub.</p></div><a href="{{ route('items.browse') }}" class="font-semibold text-blue-700 hover:text-blue-800">Browse all <span aria-hidden="true">→</span></a></div>
            @if($items->count())<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">@foreach($items as $item)<x-item-card :item="$item" />@endforeach</div><div class="mt-7">{{ $items->links() }}</div>
            @else<div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center"><h3 class="text-lg font-semibold">No available listings yet</h3><p class="mt-2 text-slate-600">Check back soon or share an item with your community.</p><div class="mt-5 flex flex-wrap justify-center gap-3"><a href="{{ route('items.browse') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">Browse items</a><a href="{{ route('items.create') }}" class="rounded-lg bg-blue-600 px-4 py-2.5 font-semibold text-white hover:bg-blue-700">Add an item</a></div></div>@endif
        </section>
    </div>
</x-app-layout>
