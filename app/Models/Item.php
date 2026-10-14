<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'category_id',
        'name',
        'description',
        'condition',
        'location',
        'price_per_day',
        'status',
        'image_path',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
    ];

    /**
     * Get the public URL for the item's image.
     */
    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('supabase')->url($this->image_path);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }
}