<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'type', 'brand', 'last_four', 'holder_name',
    'exp_month', 'exp_year', 'upi_id', 'is_default',
])]
class PaymentMethod extends Model
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

    public function label(): string
    {
        if ($this->type === 'upi') {
            return 'UPI · '.$this->upi_id;
        }

        $expiry = $this->exp_month && $this->exp_year
            ? sprintf(' · %02d/%d', $this->exp_month, $this->exp_year)
            : '';

        return trim(($this->brand ?: 'Card').' ···· '.$this->last_four.$expiry);
    }
}
