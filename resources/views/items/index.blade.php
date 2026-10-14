<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    My items
                </h1>
                <p class="mt-1 text-sm text-slate-600">
                    Manage the listings you have shared with BorrowHub.
                </p>
            </div>

            <a
                href="{{ route('items.create') }}"
                class="inline-flex min-h-11 items-center rounded-lg bg-blue-600 px-4 font-semibold text-white hover:bg-blue-700"
            >
                Add an item
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @if(session('success'))
            <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if($items->count())
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($items as $item)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        {{-- Item photo from Supabase Storage --}}
                        @if($item->image_path)
                            <img
                                src="{{ $item->imageUrl() }}"
                                alt="Photo of {{ $item->name }}"
                                loading="lazy"
                                class="mb-4 h-44 w-full rounded-xl object-cover"
                            >
                        @else
                            <div class="mb-4 flex h-44 items-center justify-center rounded-xl bg-slate-100 text-sm text-slate-500">
                                No item photo
                            </div>
                        @endif

                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm text-slate-500">
                                    {{ $item->category?->name ?? 'Uncategorized' }}
                                </p>

                                <h2 class="mt-1 break-words text-lg font-bold">
                                    {{ $item->name }}
                                </h2>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->status === 'available' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>

                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                            {{ $item->description }}
                        </p>

                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-slate-500">Condition</dt>
                                <dd class="font-medium">
                                    {{ ucwords(str_replace('_', ' ', $item->condition)) }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-slate-500">Daily price</dt>
                                <dd class="font-medium">
                                    {{ (float) $item->price_per_day === 0.0 ? 'Free' : '₱' . number_format((float) $item->price_per_day, 2) }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex flex-wrap gap-2">

                            <a
                                href="{{ route('items.edit', $item) }}"
                                class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Edit
                            </a>

                            @if($item->status === 'inactive')
                                <form method="POST" action="{{ route('items.activate', $item) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="min-h-10 rounded-lg bg-emerald-700 px-4 text-sm font-semibold text-white hover:bg-emerald-800"
                                    >
                                        Reactivate
                                    </button>
                                </form>
                            @else
                                <form
                                    method="POST"
                                    action="{{ route('items.deactivate', $item) }}"
                                    onsubmit="return confirm('Deactivate this listing?')"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="min-h-10 rounded-lg border border-red-200 px-4 text-sm font-semibold text-red-700 hover:bg-red-50"
                                    >
                                        Deactivate
                                    </button>
                                </form>
                            @endif

                        </div>
                    </article>
                @endforeach

            </div>

            <div class="mt-7">
                {{ $items->links() }}
            </div>

        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <h2 class="text-lg font-semibold">
                    You have not listed an item yet
                </h2>

                <p class="mt-2 text-slate-600">
                    Add your first listing to make it available to your community.
                </p>

                <a
                    href="{{ route('items.create') }}"
                    class="mt-5 inline-flex min-h-11 items-center rounded-lg bg-blue-600 px-5 font-semibold text-white hover:bg-blue-700"
                >
                    Add your first item
                </a>
            </div>
        @endif

    </div>
</x-app-layout>