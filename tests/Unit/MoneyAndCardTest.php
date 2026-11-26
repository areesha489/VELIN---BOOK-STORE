<?php

namespace Tests\Unit;

use App\Support\Card;
use App\Support\Pricing;
use PHPUnit\Framework\TestCase;

class MoneyAndCardTest extends TestCase
{
    public function test_luhn_accepts_a_known_test_card_and_rejects_a_bad_one(): void
    {
        $this->assertTrue(Card::valid(Card::digits('4242 4242 4242 4242')));
        $this->assertFalse(Card::valid('4242424242424241'));
        $this->assertSame('Visa', Card::brand('4242424242424242'));
        $this->assertSame('Mastercard', Card::brand('5555555555554444'));
        $this->assertSame('RuPay', Card::brand('6521456789012345'));
    }

    public function test_dispatch_is_free_over_the_threshold(): void
    {
        $this->assertSame(49.0, Pricing::shipping(500));
        $this->assertSame(0.0, Pricing::shipping(999));
        $this->assertSame(0.0, Pricing::shipping(0));
    }
}
