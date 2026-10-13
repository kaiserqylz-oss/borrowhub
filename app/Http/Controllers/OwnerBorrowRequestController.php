<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OwnerBorrowRequestController extends Controller
{
    /**
     * Display requests for items owned by the logged-in user.
     */
    public function index()
    {
        $borrowRequests = BorrowRequest::with(['item', 'borrower'])
            ->whereHas('item', function ($query) {
                $query->where('owner_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('owner.borrow-requests.index', compact('borrowRequests'));
    }

    /**
     * Approve a pending request and create its transaction.
     */
    public function approve(BorrowRequest $borrowRequest)
    {
        $this->authorizeOwner($borrowRequest);

        $result = DB::transaction(function () use ($borrowRequest) {
            // Serialize approvals for one item before checking date conflicts.
            $item = \App\Models\Item::whereKey($borrowRequest->item_id)->lockForUpdate()->firstOrFail();
            $borrowRequest->refresh();

            if ($item->status !== 'available') {
                return 'unavailable';
            }

            if ($borrowRequest->status !== 'pending') {
                return 'processed';
            }

            $hasConflict = BorrowRequest::where('item_id', $borrowRequest->item_id)
                ->where('id', '!=', $borrowRequest->id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $borrowRequest->end_date)
                ->whereDate('end_date', '>=', $borrowRequest->start_date)
                ->exists();

            if ($hasConflict) {
                return 'conflict';
            }

            $borrowRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'rejected_at' => null,
            ]);

            Transaction::firstOrCreate(
                ['borrow_request_id' => $borrowRequest->id],
                [
                    'borrowed_at' => now(),
                    'expected_return_date' => $borrowRequest->end_date,
                    'return_status' => 'not_returned',
                    'status' => 'active',
                ]
            );

            return 'approved';
        });

        if ($result === 'conflict') {
            return back()->with('error', 'This item already has an approved request during those dates.');
        }

        if ($result === 'processed') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        if ($result === 'unavailable') {
            return back()->with('error', 'This item is no longer available for new reservations.');
        }

        return back()->with('success', 'Request approved and transaction created successfully.');
    }
    /**
     * Mark a borrowed item as returned.
     */
    public function reject(BorrowRequest $borrowRequest)
    {
        $this->authorizeOwner($borrowRequest);

        $rejected = DB::transaction(function () use ($borrowRequest) {
            \App\Models\Item::whereKey($borrowRequest->item_id)->lockForUpdate()->firstOrFail();
            $borrowRequest->refresh();

            if ($borrowRequest->status !== 'pending') {
                return false;
            }

            $borrowRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'approved_at' => null,
            ]);

            return true;
        });

        if (! $rejected) {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        return back()->with('success', 'Borrowing request rejected successfully.');
    }
    public function markReturned(BorrowRequest $borrowRequest)
    {
        $this->authorizeOwner($borrowRequest);

        $result = DB::transaction(function () use ($borrowRequest) {
            $transaction = Transaction::where('borrow_request_id', $borrowRequest->id)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return 'missing';
            }

            if ($borrowRequest->status !== 'approved' || $transaction->status !== 'active') {
                return 'ineligible';
            }

            if ($transaction->return_status === 'returned' || $transaction->returned_at) {
                return 'returned';
            }

            $transaction->update([
                'returned_at' => now(),
                'return_status' => 'returned',
                'status' => 'completed',
            ]);

            return 'completed';
        });

        return match ($result) {
            'missing' => back()->with('error', 'No transaction exists for this borrowing request.'),
            'ineligible' => back()->with('error', 'Only active approved requests can be marked as returned.'),
            'returned' => back()->with('error', 'This item has already been marked as returned.'),
            default => back()->with('success', 'Item marked as returned successfully.'),
        };
    }
    private function authorizeOwner(BorrowRequest $borrowRequest): void
    {
        abort_unless(
            $borrowRequest->item()
                ->where('owner_id', auth()->id())
                ->exists(),
            403,
            'You are not authorized to manage this borrowing request.'
        );
    }
}