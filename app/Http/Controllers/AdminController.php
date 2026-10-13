<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\BorrowRequest;
use App\Models\Transaction;
use App\Models\Report;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalItems = Item::count();
        $totalRequests = BorrowRequest::count();
        $totalTransactions = Transaction::count();
        $totalReports = Report::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalItems',
            'totalRequests',
            'totalTransactions',
            'totalReports'
        ));
    }
}