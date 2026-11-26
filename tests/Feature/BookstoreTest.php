<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookstoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_catalog_search_filters_and_sorts(): void
    {
        $this->get('/books?q=salt+atlas')
            ->assertOk()
            ->assertSee('The Salt Atlas')
            ->assertDontSee('Blackwater Index');

        $this->get('/books?genre=thriller')
            ->assertOk()
            ->assertSee('The Last Signal')
            ->assertDontSee('The Salt Atlas');

        $this->get('/books?author=jonah-voss&sort=newest')
            ->assertOk()
            ->assertSee('The Last Signal')
            ->assertDontSee('Spice Routes');

        $this->get('/books?collection=bestsellers&sort=price_asc')
            ->assertOk()
            ->assertSee('Glass Orchard');
    }

    public function test_reader_can_cart_checkout_review_and_save(): void
    {
        $reader = User::query()->where('email', 'reader@velin.store')->firstOrFail();
        $book = Book::query()->where('slug', 'moss-code')->firstOrFail();
        $stock = $book->stock;

        $this->actingAs($reader)
            ->post(route('cart.store'), ['book_id' => $book->id, 'quantity' => 1])
            ->assertRedirect();

        $this->actingAs($reader)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Moss Code');

        $addressId = $reader->addresses()->value('id');
        $paymentId = $reader->paymentMethods()->value('id');

        $this->actingAs($reader)
            ->post(route('checkout.store'), [
                'address_id' => $addressId,
                'payment_method_id' => $paymentId,
            ])
            ->assertRedirect();

        $order = Order::query()->where('user_id', $reader->id)->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame('placed', $order->status);
        $this->assertSame($stock - 1, $book->fresh()->stock);
        $this->assertDatabaseHas('order_events', ['order_id' => $order->id, 'status' => 'placed']);

        $this->actingAs($reader)
            ->post(route('reviews.store', $book), [
                'rating' => 5,
                'title' => 'A ship that prints',
                'body' => 'Quinn makes the archive feel like a room you could walk through, ink and all.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['book_id' => $book->id, 'user_id' => $reader->id, 'rating' => 5]);

        $this->actingAs($reader)
            ->post(route('wishlist.toggle'), ['book_id' => $book->id])
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', ['user_id' => $reader->id, 'book_id' => $book->id]);
    }

    public function test_full_card_number_is_not_stored(): void
    {
        $reader = User::query()->where('email', 'reader@velin.store')->firstOrFail();

        $this->actingAs($reader)->post(route('payments.store'), [
            'type' => 'card',
            'holder_name' => 'Rohan Mehta',
            'card_number' => '5555 5555 5555 4444',
            'exp_month' => 12,
            'exp_year' => now()->year + 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $reader->id,
            'brand' => 'Mastercard',
            'last_four' => '4444',
        ]);

        $stored = PaymentMethod::query()->where('last_four', '4444')->firstOrFail()->toJson();
        $this->assertStringNotContainsString('5555555555554444', $stored);
    }

    public function test_readers_cannot_open_the_desk_and_social_demo_is_local_only(): void
    {
        $reader = User::query()->where('email', 'reader@velin.store')->firstOrFail();
        $admin = User::query()->where('email', 'admin@velin.store')->firstOrFail();

        $this->get('/checkout')->assertRedirect('/login');
        $this->actingAs($reader)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_social_demo_is_unavailable_outside_local(): void
    {
        $this->post('/auth/google/demo')->assertNotFound();
    }
}
