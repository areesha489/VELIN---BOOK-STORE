<?php

namespace App\Support;

use App\Models\Address;
use App\Models\PaymentMethod;
use App\Models\User;

class Defaults
{
    public static function address(User $user, Address $address, bool $makeDefault): void
    {
        $hasOtherDefault = $user->addresses()
            ->where('id', '!=', $address->id)
            ->where('is_default', true)
            ->exists();

        if ($makeDefault || ! $hasOtherDefault) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->forceFill(['is_default' => true])->save();
        }
    }

    public static function payment(User $user, PaymentMethod $method, bool $makeDefault): void
    {
        $hasOtherDefault = $user->paymentMethods()
            ->where('id', '!=', $method->id)
            ->where('is_default', true)
            ->exists();

        if ($makeDefault || ! $hasOtherDefault) {
            $user->paymentMethods()->where('id', '!=', $method->id)->update(['is_default' => false]);
            $method->forceFill(['is_default' => true])->save();
        }
    }
}
