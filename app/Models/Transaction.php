<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_request_id',
        'borrowed_at',
        'expected_return_date',
        'returned_at',
        'return_status',
        'status',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'expected_return_date' => 'date',
        'returned_at' => 'datetime',
    ];

    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}