<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with(['reportedUser', 'item', 'transaction'])
            ->where('reporter_id', $request->user()->id)
            ->latest()->paginate(10);

        return view('reports.my-index', compact('reports'));
    }

    public function adminIndex(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['open', 'under_review', 'resolved', 'dismissed'])],
        ]);
        $query = Report::with(['reporter', 'reportedUser', 'item', 'transaction'])->latest();
        if (! empty($validated['status'])) $query->where('status', $validated['status']);
        $reports = $query->paginate(15)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function createGeneral()
    {
        $items = Item::where('status', 'available')->where('owner_id', '!=', auth()->id())->orderBy('name')->get(['id', 'name']);
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get(['id', 'name']);
        $transactions = Transaction::with(['borrowRequest.item', 'borrowRequest.borrower'])
            ->whereHas('borrowRequest', fn ($query) => $query->where('borrower_id', auth()->id())->orWhereHas('item', fn ($item) => $item->where('owner_id', auth()->id())))
            ->latest()->get();

        return view('reports.create-general', compact('items', 'users', 'transactions'));
    }

    public function storeGeneral(Request $request)
    {
        $validated = $request->validate([
            'target_type' => ['required', Rule::in(['item', 'user', 'transaction'])],
            'target_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', Rule::in(['Safety concern', 'Misleading listing', 'Harassment or abuse', 'Damaged or missing item', 'Other'])],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $targetUserId = null;
        $itemId = null;
        $transactionId = null;
        if ($validated['target_type'] === 'item') {
            $target = Item::findOrFail($validated['target_id']);
            abort_if($target->owner_id === $request->user()->id, 403);
            $targetUserId = $target->owner_id;
            $itemId = $target->id;
        } elseif ($validated['target_type'] === 'user') {
            $target = User::findOrFail($validated['target_id']);
            abort_if($target->id === $request->user()->id, 403);
            $targetUserId = $target->id;
        } else {
            $target = Transaction::with('borrowRequest.item')->findOrFail($validated['target_id']);
            $borrowRequest = $target->borrowRequest;
            $ownerId = $borrowRequest->item->owner_id;
            abort_unless(in_array($request->user()->id, [$ownerId, $borrowRequest->borrower_id], true), 403);
            $transactionId = $target->id;
            $itemId = $borrowRequest->item_id;
            $targetUserId = $request->user()->id === $ownerId ? $borrowRequest->borrower_id : $ownerId;
        }

        $request->user()->reports()->create([
            'reported_user_id' => $targetUserId,
            'item_id' => $itemId,
            'transaction_id' => $transactionId,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('reports.index')->with('success', 'Your report was submitted to the moderation team.');
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['under_review', 'resolved', 'dismissed'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'resolved_at' => in_array($validated['status'], ['resolved', 'dismissed'], true) ? now() : null,
        ]);

        return back()->with('success', 'Report status updated.');
    }
    public function create(Item $item)
    {
        abort_if($item->owner_id === auth()->id(), 403);

        return view('reports.create', compact('item'));
    }

    public function store(Request $request, Item $item)
    {
        abort_if($item->owner_id === $request->user()->id, 403);

        $validated = $request->validate([
            'reason' => ['required', Rule::in(['Safety concern', 'Misleading listing', 'Harassment or abuse', 'Damaged or missing item', 'Other'])],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $request->user()->reports()->create([
            'reported_user_id' => $item->owner_id,
            'item_id' => $item->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('items.show', $item)->with('success', 'Your report has been submitted for review.');
    }
}