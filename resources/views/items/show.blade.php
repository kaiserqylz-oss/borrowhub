<x-app-layout>
    <x-slot name="header">
        <p class="text-sm">
            <a href="{{ route('items.browse') }}" class="font-medium text-blue-700 hover:underline">
                ← Browse items
            </a>
        </p>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        @if(session('success'))
            <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Item photo from Supabase Storage --}}
            @if($item->image_path)
                <img
                    src="{{ $item->imageUrl() }}"
                    alt="Photo of {{ $item->name }}"
                    class="max-h-[28rem] w-full bg-slate-100 object-cover"
                >
            @else
                <div class="flex h-64 items-center justify-center bg-slate-100 text-slate-500">
                    No photo available
                </div>
            @endif

            <div class="p-5 sm:p-8">

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-blue-700">
                            {{ $item->category?->name ?? 'Community item' }}
                        </p>

                        <h1 class="mt-1 break-words text-3xl font-bold tracking-tight">
                            {{ $item->name }}
                        </h1>

                        <p class="mt-2 text-sm text-slate-600">
                            Shared by {{ $item->owner?->name ?? 'a community member' }}
                        </p>
                    </div>

                    <span class="rounded-full px-3 py-1.5 text-sm font-semibold {{ $isAvailable ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-900' }}">
                        {{ $isAvailable ? 'Available' : 'Currently borrowed' }}
                    </span>
                </div>

                <div class="mt-7 grid gap-7 lg:grid-cols-[1fr_18rem]">

                    {{-- Item information --}}
                    <div>
                        <h2 class="text-lg font-semibold">About this item</h2>

                        <p class="mt-2 whitespace-pre-line break-words leading-7 text-slate-700">
                            {{ $item->description }}
                        </p>

                        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm text-slate-500">Condition</dt>
                                <dd class="mt-1 font-semibold">
                                    {{ ucwords(str_replace('_', ' ', $item->condition)) }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm text-slate-500">Location</dt>
                                <dd class="mt-1 break-words font-semibold">
                                    {{ $item->location }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm text-slate-500">Category</dt>
                                <dd class="mt-1 font-semibold">
                                    {{ $item->category?->name ?? '—' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm text-slate-500">Owner</dt>
                                <dd class="mt-1 font-semibold">
                                    {{ $item->owner?->name ?? 'Community member' }}
                                </dd>
                            </div>

                        </dl>
                    </div>

                    {{-- Rental details and actions --}}
                    <aside class="h-fit rounded-xl border border-slate-200 p-5">

                        <p class="text-sm text-slate-500">Rental price</p>

                        <p class="mt-1 text-2xl font-bold">
                            @if((float) $item->price_per_day === 0.0)
                                Free
                            @else
                                ₱{{ number_format((float) $item->price_per_day, 2) }}
                                <span class="text-base font-normal text-slate-500">/ day</span>
                            @endif
                        </p>

                        <p class="mt-2 text-sm text-slate-600">
                            Your total is calculated from the requested dates.
                        </p>

                        <div class="mt-5 grid gap-2">

                            @auth
                                @if(auth()->id() !== $item->owner_id)

                                    @if($isAvailable)
                                        <a
                                            href="{{ route('borrow-requests.create', $item) }}"
                                            class="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-600 px-4 font-semibold text-white hover:bg-blue-700"
                                        >
                                            Request to borrow
                                        </a>
                                    @else
                                        <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-900">
                                            This item is currently on an approved borrowing transaction and cannot be requested right now.
                                        </p>
                                    @endif

                                    <a
                                        href="{{ route('items.report.create', $item) }}"
                                        class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                    >
                                        Report this listing
                                    </a>

                                @else
                                    <a
                                        href="{{ route('items.edit', $item) }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-600 px-4 font-semibold text-white"
                                    >
                                        Edit your listing
                                    </a>
                                @endif

                            @else

                                @if($isAvailable)
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-600 px-4 font-semibold text-white"
                                    >
                                        Log in to request
                                    </a>
                                @else
                                    <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-900">
                                        This item is currently being borrowed.
                                    </p>
                                @endif

                            @endauth

                        </div>
                    </aside>

                </div>
            </div>
        </article>
    </div>
</x-app-layout>