<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;

class MyBorrowRequestController extends Controller
{
    /**
     * Display the logged-in user's borrowing requests.
     */
    public function index()
    {
        $borrowRequests = BorrowRequest::with(['item', 'transaction.ratings'])
            ->where('borrower_id', auth()->id())
            ->latest()
            ->get();

        return view('borrow-requests.index', compact('borrowRequests'));
    }
}