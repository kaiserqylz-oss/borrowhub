<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Borrow Requests
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if (session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error message --}}
            @if (session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Incoming Requests
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Review requests from people who want to borrow your items.
                    </p>
                </div>

                @forelse ($borrowRequests as $borrowRequest)

                    @php
                        $status = strtolower($borrowRequest->status);

                        $transaction = $borrowRequest->transaction;

                        if ($transaction && $transaction->return_status === 'returned') {
                            $status = 'returned';
                        }

                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                            'returned' => 'bg-blue-100 text-blue-800',
                        ];
                    @endphp

                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 mb-5">

                        {{-- Item and request status --}}
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $borrowRequest->item->name }}
                                </h4>

                                <p class="mt-1 text-sm text-gray-500">
                                    Request #{{ $borrowRequest->id }}
                                </p>

                                <p class="mt-2 text-gray-700 dark:text-gray-300">
                                    Requested by:
                                    <span class="font-semibold">
                                        {{ $borrowRequest->borrower->name }}
                                    </span>
                                </p>
                            </div>

                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>

                        {{-- Request details --}}
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div>
                                <p class="text-sm text-gray-500">Start Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $borrowRequest->start_date->format('M d, Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Return Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $borrowRequest->end_date->format('M d, Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Duration</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $borrowRequest->duration_days }}
                                    {{ $borrowRequest->duration_days == 1 ? 'day' : 'days' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    @if ((float) $borrowRequest->total_amount === 0.0)
                                        Free
                                    @else
                                        ₱{{ number_format((float) $borrowRequest->total_amount, 2) }}
                                    @endif
                                </p>
                            </div>

                        </div>

                        {{-- Borrower's message --}}
                        @if ($borrowRequest->message)
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Message from Borrower</p>

                                <p class="mt-1 text-gray-800 dark:text-gray-200 whitespace-pre-line">
                                    {{ $borrowRequest->message }}
                                </p>
                            </div>
                        @endif

                        {{-- Submission date --}}
                        <div class="mt-4 text-sm text-gray-500">
                            Submitted:
                            {{ $borrowRequest->requested_at?->format('M d, Y h:i A') ?? $borrowRequest->created_at->format('M d, Y h:i A') }}
                        </div>

                        {{-- Approve or reject pending requests --}}
                        @if ($status === 'pending')
                            <div class="mt-5 flex flex-wrap gap-3">

                                <form
                                    action="{{ route('owner.borrow-requests.approve', $borrowRequest) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to approve this request?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                                    >
                                        Approve Request
                                    </button>
                                </form>

                                <form
                                    action="{{ route('owner.borrow-requests.reject', $borrowRequest) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to reject this request?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="px-5 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                    >
                                        Reject Request
                                    </button>
                                </form>

                            </div>
                        @endif

                        {{-- Mark approved items as returned --}}
                        @if (
                            $borrowRequest->status === 'approved'
                            && $transaction
                            && $transaction->return_status !== 'returned'
                        )
                            <div class="mt-5">
                                <form
                                    action="{{ route('owner.borrow-requests.return', $borrowRequest) }}"
                                    method="POST"
                                    onsubmit="return confirm('Confirm that this item has been returned?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                    >
                                        Mark as Returned
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Show return date once returned --}}
                        @if ($transaction && $transaction->return_status === 'returned')
                            <div class="mt-5 p-3 rounded-lg bg-blue-50 text-blue-800">
                                <p class="font-semibold">
                                    Item returned successfully.
                                </p>

                                @if ($transaction->returned_at)
                                    <p class="mt-1 text-sm">
                                        Returned on:
                                        {{ $transaction->returned_at->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                    </div>

                @empty

                    <div class="text-center py-10">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                            No Borrow Requests Yet
                        </h4>

                        <p class="mt-2 text-gray-500">
                            When someone requests one of your items, it will appear here.
                        </p>

                        <a
                            href="{{ route('items.index') }}"
                            class="inline-block mt-5 px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            View My Items
                        </a>
                    </div>

                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>