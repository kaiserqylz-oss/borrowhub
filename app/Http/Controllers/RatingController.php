<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = \App\Models\Rating::with(['reviewer', 'reviewedUser', 'transaction.borrowRequest.item'])
            ->latest()->paginate(12);
        $average = \App\Models\Rating::query()->avg('rating');
        $reviewCount = \App\Models\Rating::count();

        return view('reviews.index', compact('ratings', 'average', 'reviewCount'));
    }

    public function store(Request $request, Transaction $transaction)
    {
        $borrowRequest = $transaction->borrowRequest()->with('item')->firstOrFail();
        abort_unless($borrowRequest->borrower_id === $request->user()->id, 403);

        if ($transaction->status !== 'completed' || $transaction->return_status !== 'returned') {
            return back()->with('error', 'You can rate the owner after the item has been returned.');
        }

        if ($transaction->ratings()->where('reviewer_id', $request->user()->id)->exists()) {
            return back()->with('error', 'You have already rated this transaction.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $transaction->ratings()->create([
            'reviewer_id' => $request->user()->id,
            'reviewed_user_id' => $borrowRequest->item->owner_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Your rating has been submitted.');
    }
}
