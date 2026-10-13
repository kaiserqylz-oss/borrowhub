<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Add Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-semibold mb-6">
                        List an Item
                    </h3>

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Item Name -->
                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm mb-2">
                                Item Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                placeholder="Example: Cordless Drill"
                                required
                            >
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block font-medium text-sm mb-2">
                                Category
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >
                                <option value="">Select a category</option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm mb-2">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                placeholder="Describe the item, including important details or limitations."
                                required
                            >{{ old('description') }}</textarea>
                        </div>

                        <!-- Condition -->
                        <div class="mb-4">
                            <label for="condition" class="block font-medium text-sm mb-2">
                                Condition
                            </label>

                            <select
                                id="condition"
                                name="condition"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >
                                <option value="">Select condition</option>
                                <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>
                                    New
                                </option>
                                <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>
                                    Like New
                                </option>
                                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>
                                    Good
                                </option>
                                <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>
                                    Fair
                                </option>
                                <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>
                                    Poor
                                </option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div class="mb-4">
                            <label for="location" class="block font-medium text-sm mb-2">
                                Location
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="{{ old('location') }}"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                placeholder="Example: San Fernando City, La Union"
                                required
                            >
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <label for="price_per_day" class="block font-medium text-sm mb-2">
                                Price per Day (₱)
                            </label>

                            <input
                                type="number"
                                id="price_per_day"
                                name="price_per_day"
                                value="{{ old('price_per_day', 0) }}"
                                min="0"
                                step="0.01"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >

                            <p class="text-sm text-gray-500 mt-1">
                                Enter 0 if the item is free to borrow.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="image" class="block font-medium text-sm mb-2">Item Photo (optional)</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, or WebP; maximum 5 MB.</p>
                            @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <!-- Buttons -->
                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            >
                                List Item
                            </button>

                            <a
                                href="{{ route('items.index') }}"
                                class="px-5 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                            >
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>