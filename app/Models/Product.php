<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'subcategory_id',
        'name',
        'slug',
        'description',
        'image_path',
        'old_price',
        'new_price',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
        ];
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            Category::class,
            Subcategory::class,
            'id',
            'id',
            'subcategory_id',
            'category_id',
        );
    }
}
