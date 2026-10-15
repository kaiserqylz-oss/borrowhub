<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'borrower_id',
        'start_date',
        'end_date',
        'duration_days',
        'total_amount',
        'message',
        'status',
        'requested_at',
        'approved_at',
        'rejected_at',
        'agreement_accepted_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'agreement_accepted_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}