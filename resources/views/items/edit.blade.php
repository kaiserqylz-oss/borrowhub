```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="mb-6 text-lg font-semibold">
                        Edit Item Details
                    </h3>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('items.update', $item) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        {{-- Item name --}}
                        <div class="mb-4">
                            <label for="name" class="mb-2 block text-sm font-medium">
                                Item Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $item->name) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                required
                            >
                        </div>

                        {{-- Category --}}
                        <div class="mb-4">
                            <label for="category_id" class="mb-2 block text-sm font-medium">
                                Category
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
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

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="mb-2 block text-sm font-medium">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                required
                            >{{ old('description', $item->description) }}</textarea>
                        </div>

                        {{-- Condition --}}
                        <div class="mb-4">
                            <label for="condition" class="mb-2 block text-sm font-medium">
                                Condition
                            </label>

                            <select
                                id="condition"
                                name="condition"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
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

                        {{-- Location --}}
                        <div class="mb-4">
                            <label for="location" class="mb-2 block text-sm font-medium">
                                Location
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="{{ old('location', $item->location) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                required
                            >
                        </div>

                        {{-- Daily rental price --}}
                        <div class="mb-6">
                            <label for="price_per_day" class="mb-2 block text-sm font-medium">
                                Price per Day (₱)
                            </label>

                            <input
                                type="number"
                                id="price_per_day"
                                name="price_per_day"
                                value="{{ old('price_per_day', $item->price_per_day) }}"
                                min="0"
                                step="0.01"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                required
                            >
                        </div>

                        {{-- Item photo from Supabase Storage --}}
                        <div class="mb-6">
                            <label for="image" class="mb-2 block text-sm font-medium">
                                Item Photo
                            </label>

                            @if ($item->image_path)
                                <img
                                    src="{{ $item->imageUrl() }}"
                                    alt="Photo of {{ $item->name }}"
                                    class="mb-3 h-40 rounded-lg object-cover"
                                >

                                <label class="mb-3 flex items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        name="remove_image"
                                        value="1"
                                    >
                                    Remove current photo
                                </label>
                            @endif

                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700"
                            >

                            <p class="mt-1 text-sm text-gray-500">
                                JPG, PNG, or WebP; maximum 5 MB.
                            </p>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Form actions --}}
                        <div class="flex flex-wrap gap-3">
                            <button
                                type="submit"
                                class="rounded-md bg-blue-600 px-5 py-2 text-white hover:bg-blue-700"
                            >
                                Save Changes
                            </button>

                            <a
                                href="{{ route('items.index') }}"
                                class="rounded-md bg-gray-500 px-5 py-2 text-white hover:bg-gray-600"
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
```