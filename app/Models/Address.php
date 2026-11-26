<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'label', 'recipient', 'phone', 'line1', 'line2',
    'city', 'state', 'postal_code', 'country', 'is_default',
])]
class Address extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function block(): string
    {
        $lines = array_filter([
            $this->recipient,
            $this->line1,
            $this->line2,
            $this->city.', '.$this->state.' '.$this->postal_code,
            $this->country,
            $this->phone,
        ]);

        return implode("\n", $lines);
    }
}
