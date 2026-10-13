<nav x-data="{ open: false, accountOpen: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4">
            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="shrink-0 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600" aria-label="BorrowHub home">
                <x-application-logo class="text-lg" />
            </a>
            <div class="hidden items-center gap-1 lg:flex">
                @auth
                    @php($links = [
                        ['Dashboard', route('dashboard'), request()->routeIs('dashboard')],
                        ['My Items', route('items.index'), request()->routeIs('items.index','items.create','items.edit')],
                        ['Browse', route('items.browse'), request()->routeIs('items.browse','items.show')],
                        ['My Requests', route('borrow-requests.index'), request()->routeIs('borrow-requests.index')],
                        ['Manage Requests', route('owner.borrow-requests.index'), request()->routeIs('owner.borrow-requests.*')],
                        ['Reports', route('reports.index'), request()->routeIs('reports.*','items.report.*')],
                        ['Reviews', route('reviews.index'), request()->routeIs('reviews.*')],
                    ])
                    @foreach($links as [$label, $href, $active])
                        <a href="{{ $href }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600', 'bg-blue-50 text-blue-700' => $active, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => !$active]) @if($active) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.reports.index') }}" @class(['rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600', 'bg-blue-50 text-blue-700' => request()->routeIs('admin.reports.*'), 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => !request()->routeIs('admin.reports.*')])>Moderation</a>
                    @endif
                @else
                    <a href="{{ route('items.browse') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">Browse</a>
                @endauth
            </div>
            <div class="hidden items-center gap-3 lg:flex">
                <button type="button" data-install-app hidden class="min-h-10 rounded-lg border border-blue-200 bg-blue-50 px-3 text-sm font-semibold text-blue-800 hover:bg-blue-100">Install app</button>
                @auth
                    <div class="relative" @click.outside="accountOpen=false">
                        <button type="button" @click="accountOpen=!accountOpen" :aria-expanded="accountOpen.toString()" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm font-medium text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="max-w-32 truncate">{{ auth()->user()->name }}</span>
                            <span aria-hidden="true">⌄</span>
                        </button>
                        <div x-cloak x-show="accountOpen" x-transition class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Log out</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:text-blue-700">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Create account</a>
                @endauth
            </div>
            <button type="button" @click="open=!open" :aria-expanded="open.toString()" aria-controls="mobile-navigation" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 lg:hidden" :aria-label="open ? 'Close menu' : 'Open menu'">
                <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-navigation" x-cloak x-show="open" x-transition class="border-t border-slate-200 bg-white px-4 pb-4 pt-2 lg:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 sm:grid-cols-2">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
                <a href="{{ route('items.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">My Items</a>
                <a href="{{ route('items.browse') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Browse</a>
                <a href="{{ route('borrow-requests.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">My Borrow Requests</a>
                <a href="{{ route('owner.borrow-requests.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Manage Borrow Requests</a>
                <a href="{{ route('reports.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reports</a>
                <a href="{{ route('reviews.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reviews</a>
                @if(auth()->user()->role === 'admin')<a href="{{ route('admin.reports.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Moderation queue</a>@endif
                <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Profile</a>
                <button type="button" data-install-app hidden class="min-h-11 rounded-lg border border-blue-200 bg-blue-50 px-3 text-sm font-semibold text-blue-800 hover:bg-blue-100">Install BorrowHub</button>
                <form method="POST" action="{{ route('logout') }}" class="sm:col-span-2">@csrf<button class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50">Log out</button></form>
            @else
                <a href="{{ route('items.browse') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Browse Items</a>
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Log in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Create account</a>
                <button type="button" data-install-app hidden class="min-h-11 rounded-lg border border-blue-200 bg-blue-50 px-3 text-sm font-semibold text-blue-800 hover:bg-blue-100">Install BorrowHub</button>
            @endauth
        </div>
    </div>
</nav>
