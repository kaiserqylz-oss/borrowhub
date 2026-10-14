<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            BorrowHub Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    Welcome, Administrator
                </h3>

                <p class="mt-2 text-gray-600">
                    Manage BorrowHub reports and monitor platform activity.
                </p>

                <div class="mt-6">
                    <a href="{{ route('admin.reports.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Manage Reports
                    </a>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard') }}"
                       class="text-sm text-blue-600 hover:underline">
                        Return to Member Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>