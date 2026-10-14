<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class ItemController extends Controller
{
    /**
     * Browse available items.
     */
    public function browse(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('status', 'active'),
            ],
        ]);

        $query = Item::with(['owner', 'category'])
            ->where('status', 'available');

        if (auth()->check()) {
            $query->where('owner_id', '!=', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('description', 'ilike', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('items.browse', compact('items', 'categories'));
    }

    /**
     * Display the details of a single item.
     */
    public function show(Item $item)
    {
        $item->load(['owner', 'category']);

        if ($item->status !== 'available') {
            abort(404);
        }

        $isAvailable = ! $item->borrowRequests()
            ->where('status', 'approved')
            ->whereHas('transaction', fn ($transaction) => $transaction->where('status', 'active'))
            ->exists();

        return view('items.show', compact('item', 'isAvailable'));
    }

    /**
     * Display the member's items.
     */
    public function index()
    {
        $items = Item::where('owner_id', auth()->id())
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('status', 'active'),
            ],
            'description' => ['required', 'string', 'max:10000'],
            'condition' => ['required', 'in:new,like_new,good,fair,poor'],
            'location' => ['required', 'string', 'max:255'],
            'price_per_day' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'supabase');

            if (! $imagePath) {
                throw new RuntimeException('The item image could not be stored in Supabase.');
            }
        }

        unset($validated['image']);

        $validated['owner_id'] = $request->user()->id;
        $validated['status'] = 'available';
        $validated['image_path'] = $imagePath;

        try {
            Item::create($validated);
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('supabase')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('items.index')
            ->with('success', 'Item listed successfully.');
    }

    /**
     * Update an existing item.
     */
    public function update(Request $request, Item $item)
    {
        abort_if($item->owner_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('status', 'active'),
            ],
            'description' => ['required', 'string', 'max:10000'],
            'condition' => ['required', 'in:new,like_new,good,fair,poor'],
            'location' => ['required', 'string', 'max:255'],
            'price_per_day' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $oldImagePath = $item->image_path;
        $removeImage = $request->boolean('remove_image');
        $newImagePath = null;

        unset($validated['image'], $validated['remove_image']);

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('items', 'supabase');

            if (! $newImagePath) {
                throw new RuntimeException('The replacement image could not be stored in Supabase.');
            }

            $validated['image_path'] = $newImagePath;
        } elseif ($removeImage) {
            $validated['image_path'] = null;
        }

        try {
            $item->update($validated);
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('supabase')->delete($newImagePath);
            }

            throw $exception;
        }

        if (($newImagePath || $removeImage) && $oldImagePath) {
            Storage::disk('supabase')->delete($oldImagePath);
        }

        return redirect()
            ->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Reactivate an item.
     */
    public function activate(Item $item)
    {
        abort_if($item->owner_id !== auth()->id(), 403);

        $item->update([
            'status' => 'available',
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item reactivated successfully.');
    }
}