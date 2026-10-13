<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-semibold mb-6">
                        Edit Item Details
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

                    <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm mb-2">
                                Item Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $item->name) }}"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >
                        </div>

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
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm mb-2">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >{{ old('description', $item->description) }}</textarea>
                        </div>

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
                                <option value="new" {{ old('condition', $item->condition) == 'new' ? 'selected' : '' }}>
                                    New
                                </option>

                                <option value="like_new" {{ old('condition', $item->condition) == 'like_new' ? 'selected' : '' }}>
                                    Like New
                                </option>

                                <option value="good" {{ old('condition', $item->condition) == 'good' ? 'selected' : '' }}>
                                    Good
                                </option>

                                <option value="fair" {{ old('condition', $item->condition) == 'fair' ? 'selected' : '' }}>
                                    Fair
                                </option>

                                <option value="poor" {{ old('condition', $item->condition) == 'poor' ? 'selected' : '' }}>
                                    Poor
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="block font-medium text-sm mb-2">
                                Location
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="{{ old('location', $item->location) }}"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label for="price_per_day" class="block font-medium text-sm mb-2">
                                Price per Day (₱)
                            </label>

                            <input
                                type="number"
                                id="price_per_day"
                                name="price_per_day"
                                value="{{ old('price_per_day', $item->price_per_day) }}"
                                min="0"
                                step="0.01"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label for="image" class="block font-medium text-sm mb-2">Item Photo</label>
                            @if ($item->image_path)
                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="mb-3 h-40 rounded-lg object-cover">
                                <label class="mb-3 flex items-center gap-2 text-sm"><input type="checkbox" name="remove_image" value="1"> Remove current photo</label>
                            @endif
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, or WebP; maximum 5 MB.</p>
                            @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex gap-3">

                            <button
                                type="submit"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            >
                                Save Changes
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