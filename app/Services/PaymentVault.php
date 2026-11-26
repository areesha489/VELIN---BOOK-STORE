<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\User;
use App\Support\Card;
use App\Support\Defaults;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PaymentVault
{
    /**
     * Store a display-only payment method. The full card number is reduced to a brand and last four digits.
     *
     * @param  array<string, mixed>  $input
     */
    public function store(User $user, array $input, bool $makeDefault): PaymentMethod
    {
        if (($input['type'] ?? 'card') === 'upi') {
            $upi = strtolower(trim((string) ($input['upi_id'] ?? '')));

            if (! preg_match('/^[a-z0-9.\-_]{2,}@[a-z]{2,}$/', $upi)) {
                throw ValidationException::withMessages([
                    'upi_id' => 'Enter a UPI id like name@bank.',
                ]);
            }

            $method = $user->paymentMethods()->create([
                'type' => 'upi',
                'upi_id' => $upi,
                'holder_name' => trim((string) ($input['holder_name'] ?? '')) ?: $user->name,
                'is_default' => false,
            ]);
        } else {
            $digits = Card::digits((string) ($input['card_number'] ?? ''));
            $month = (int) ($input['exp_month'] ?? 0);
            $year = (int) ($input['exp_year'] ?? 0);
            $holder = trim((string) ($input['holder_name'] ?? ''));

            if (! Card::valid($digits)) {
                throw ValidationException::withMessages([
                    'card_number' => 'That card number does not look valid.',
                ]);
            }

            if ($holder === '') {
                throw ValidationException::withMessages([
                    'holder_name' => 'Enter the name on the card.',
                ]);
            }

            if ($month < 1 || $month > 12 || $year < (int) date('Y') || $year > (int) date('Y') + 15) {
                throw ValidationException::withMessages([
                    'exp_month' => 'Enter a current expiry date.',
                ]);
            }

            if (Carbon::createFromDate($year, $month, 1)->endOfMonth()->isPast()) {
                throw ValidationException::withMessages([
                    'exp_month' => 'That card has expired.',
                ]);
            }

            $method = $user->paymentMethods()->create([
                'type' => 'card',
                'brand' => Card::brand($digits),
                'last_four' => substr($digits, -4),
                'holder_name' => $holder,
                'exp_month' => $month,
                'exp_year' => $year,
                'is_default' => false,
            ]);
        }

        Defaults::payment($user, $method, $makeDefault);

        return $method->refresh();
    }
}
