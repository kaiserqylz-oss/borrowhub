<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Request to Borrow
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold text-gray-900">
                    {{ $item->name }}
                </h3>

                <p class="mt-2 text-gray-600">
                    {{ $item->description }}
                </p>

                <p class="mt-3 font-semibold text-gray-800">
                    Rental Price:
                    ₱{{ number_format((float) $item->price_per_day, 2) }} per day
                </p>

                <form
                    action="{{ route('borrow-requests.store', $item) }}"
                    method="POST"
                    class="mt-6 space-y-5"
                >
                    @csrf

                    <div>
                        <label for="start_date"
                               class="block font-medium text-sm text-gray-700">
                            Start Date
                        </label>

                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            min="{{ now()->toDateString() }}"
                            value="{{ old('start_date') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />

                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date"
                               class="block font-medium text-sm text-gray-700">
                            Return Date
                        </label>

                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            min="{{ old('start_date', now()->toDateString()) }}"
                            value="{{ old('end_date') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />

                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="message"
                               class="block font-medium text-sm text-gray-700">
                            Message to Item Owner (Optional)
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            maxlength="1000"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Tell the owner why you want to borrow this item..."
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="border border-gray-200 rounded-md p-4">
                        <h4 class="font-semibold text-gray-900">
                            Borrowing Agreement
                        </h4>

                        <p class="mt-2 text-sm text-gray-600">
                            Before submitting your request, read and agree to
                            the rules governing item borrowing, cancellations,
                            returns, damage, and disputes.
                        </p>

                        <div class="mt-3 flex items-start gap-3">
                            <input
                                type="checkbox"
                                id="agreement_accepted"
                                name="agreement_accepted"
                                value="1"
                                {{ old('agreement_accepted') ? 'checked' : '' }}
                                required
                                class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm"
                            />

                            <label for="agreement_accepted"
                                   class="text-sm text-gray-700">
                                I have read and agree to the
                                <a
                                    href="{{ route('rules') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 underline hover:text-blue-800"
                                >
                                    Rules and Regulations
                                </a>
                                and the
                                <a
                                    href="{{ route('borrowing-agreement') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 underline hover:text-blue-800"
                                >
                                    Borrowing Agreement and Transaction Rules
                                </a>.
                            </label>
                        </div>

                        @error('agreement_accepted')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if ($errors->any())
                        <div class="text-sm text-red-600">
                            Please correct the errors above and try again.
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Submit Request
                        </button>

                        <a
                            href="{{ route('items.show', $item) }}"
                            class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>