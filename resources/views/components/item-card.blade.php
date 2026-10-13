@props(['item'])
<article class="flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md">
    <a href="{{ route('items.show', $item) }}" class="block bg-slate-100 focus-visible:outline-offset-[-3px]">
        @if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="Photo of {{ $item->name }}" loading="lazy" class="h-48 w-full object-cover">
        @else<div class="flex h-48 items-center justify-center bg-slate-100 text-slate-400" aria-label="No photo available"><svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 5h16v14H4z"/><circle cx="9" cy="10" r="1.5"/><path d="m20 15-5-5L5 19"/></svg></div>@endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $item->category?->name ?? 'Community item' }}</p><h3 class="mt-1 line-clamp-2 text-lg font-bold leading-6 text-slate-900"><a href="{{ route('items.show', $item) }}" class="hover:text-blue-700">{{ $item->name }}</a></h3></div><span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Available</span></div>
        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $item->description }}</p>
        <div class="mt-auto pt-4"><p class="font-bold text-slate-900">@if((float)$item->price_per_day === 0.0)Free @else₱{{ number_format((float)$item->price_per_day, 2) }}<span class="font-normal text-slate-500"> / day</span>@endif</p><div class="mt-2 flex min-w-0 justify-between gap-2 text-sm text-slate-500"><span class="truncate">{{ $item->location }}</span><span class="truncate">{{ $item->owner?->name ?? 'Community member' }}</span></div><a href="{{ route('items.show', $item) }}" class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700">View details</a></div>
    </div>
</article>
