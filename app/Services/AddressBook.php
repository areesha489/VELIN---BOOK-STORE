<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use App\Support\Defaults;
use Illuminate\Support\Facades\Validator;

class AddressBook
{
    public function store(User $user, array $input, bool $makeDefault): Address
    {
        $data = $this->validated($input);

        $address = $user->addresses()->create([
            'label' => $data['label'] ?: 'Home',
            'recipient' => $data['recipient'],
            'phone' => $data['phone'],
            'line1' => $data['line1'],
            'line2' => $data['line2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'] ?: 'India',
            'is_default' => false,
        ]);

        Defaults::address($user, $address, $makeDefault);

        return $address->refresh();
    }

    public function update(Address $address, array $input, bool $makeDefault): Address
    {
        $data = $this->validated($input);

        $address->update([
            'label' => $data['label'] ?: 'Home',
            'recipient' => $data['recipient'],
            'phone' => $data['phone'],
            'line1' => $data['line1'],
            'line2' => $data['line2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'] ?: 'India',
        ]);

        Defaults::address($address->user, $address, $makeDefault);

        return $address->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(array $input): array
    {
        return Validator::make($input, [
            'label' => ['nullable', 'string', 'max:40'],
            'recipient' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'line1' => ['required', 'string', 'max:180'],
            'line2' => ['nullable', 'string', 'max:180'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'string', 'max:12'],
            'country' => ['nullable', 'string', 'max:80'],
        ])->validate();
    }
}
