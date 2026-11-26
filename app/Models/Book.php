<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'author_id', 'genre_id', 'title', 'slug', 'subtitle', 'excerpt', 'description',
    'price', 'compare_at', 'isbn', 'pages', 'language', 'format', 'published_at',
    'stock', 'popularity', 'is_bestseller', 'is_featured', 'cover_key', 'cover_path',
])]
class Book extends Model
{
    public const NEW_WINDOW_MONTHS = 10;

    public const COVERS = [
        'ember' => ['#2a120c', '#ff4d1c', '#f4efe4'],
        'tide' => ['#10242b', '#7fd1c7', '#f4efe4'],
        'moss' => ['#142117', '#d6f25c', '#f7f3ea'],
        'ink' => ['#16141c', '#d7c4ff', '#f4efe4'],
        'wine' => ['#2a1018', '#ff8aa0', '#f4efe4'],
        'sand' => ['#2a2116', '#e6c27a', '#f4efe4'],
        'night' => ['#10141c', '#9bb6ff', '#f4efe4'],
        'citrus' => ['#24180d', '#ffb020', '#f4efe4'],
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at' => 'decimal:2',
            'published_at' => 'date',
            'is_bestseller' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeForShelf(Builder $query): Builder
    {
        return $query
            ->with(['author:id,name,slug', 'genre:id,name,slug,accent'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }

    public function scopeBestsellers(Builder $query): Builder
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeNewArrivals(Builder $query): Builder
    {
        return $query->where('published_at', '>=', now()->subMonths(self::NEW_WINDOW_MONTHS));
    }

    public function isNew(): bool
    {
        return $this->published_at !== null
            && $this->published_at->gte(now()->subMonths(self::NEW_WINDOW_MONTHS)->startOfDay());
    }

    public function onSale(): bool
    {
        return $this->compare_at !== null && (float) $this->compare_at > (float) $this->price;
    }
}
