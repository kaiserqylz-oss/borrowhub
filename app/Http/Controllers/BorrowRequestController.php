<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BorrowRequestController extends Controller
{
    /**
     * Show the borrowing request form.
     */
    public function create(Item $item)
    {
        abort_unless($item->status === 'available', 404);

        // Users cannot borrow their own items.
        abort_if($item->owner_id === auth()->id(), 403);

        return view('borrow-requests.create', compact('item'));
    }

    /**
     * Save a borrowing request.
     */
    public function store(Request $request, Item $item)
    {
        abort_unless($item->status === 'available', 404);

        abort_if($item->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'message' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Count both the start and end dates.
        $durationDays = $startDate->diffInDays($endDate) + 1;

        // Calculate the amount using the item's saved daily price.
        $totalAmount = $durationDays * (float) $item->price_per_day;

        BorrowRequest::create([
            'item_id' => $item->id,
            'borrower_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration_days' => $durationDays,
            'total_amount' => $totalAmount,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Your borrowing request has been submitted.');
    }
}
