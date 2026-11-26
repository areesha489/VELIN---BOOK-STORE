<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Support\Pricing;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function findForDisplay(): ?Cart
    {
        return $this->findExisting();
    }

    public function count(): int
    {
        $cart = $this->findExisting();

        if (! $cart) {
            return 0;
        }

        return (int) $cart->items()->sum('quantity');
    }

    public function current(): Cart
    {
        if ($existing = $this->findExisting()) {
            return $existing;
        }

        if ($user = auth()->user()) {
            return Cart::create(['user_id' => $user->id]);
        }

        $token = (string) Str::uuid();
        session(['cart_token' => $token]);

        return Cart::create(['session_token' => $token]);
    }

    public function add(Book $book, int $quantity): string
    {
        if ($book->stock < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'That title is out of copies.',
            ]);
        }

        $quantity = max(1, min(20, $quantity));
        $cart = $this->current();
        $item = $cart->items()->firstOrNew(['book_id' => $book->id]);
        $desired = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->quantity = min($book->stock, $desired);
        $item->save();

        if ($item->quantity < $desired) {
            return 'Only '.$book->stock.' copies of '.$book->title.' are in the stacks. The cart was adjusted.';
        }

        return $book->title.' is in the cart.';
    }

    public function setQuantity(CartItem $item, int $quantity): void
    {
        $book = $item->book()->firstOrFail();

        if ($book->stock < 1) {
            $item->delete();

            throw ValidationException::withMessages([
                'quantity' => $book->title.' is no longer in stock.',
            ]);
        }

        $item->update([
            'quantity' => min(max(1, $quantity), 20, $book->stock),
        ]);
    }

    /**
     * @return array{subtotal: float, shipping: float, total: float}
     */
    public function totals(Cart $cart): array
    {
        $subtotal = (float) $cart->items->sum(fn (CartItem $item) => (float) $item->book->price * $item->quantity);
        $shipping = Pricing::shipping($subtotal);

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    public function mergeIntoUser(User $user): void
    {
        $token = session('cart_token');
        $sessionCart = $token ? Cart::query()->where('session_token', $token)->first() : null;
        $userCart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

        if ($sessionCart && $sessionCart->id !== $userCart->id) {
            foreach ($sessionCart->items()->with('book')->get() as $item) {
                $existing = $userCart->items()->where('book_id', $item->book_id)->first();
                $stock = $item->book?->stock ?? $item->quantity;

                if ($existing) {
                    $existing->update([
                        'quantity' => min($stock, 20, $existing->quantity + $item->quantity),
                    ]);
                    $item->delete();
                } else {
                    $item->update([
                        'cart_id' => $userCart->id,
                        'quantity' => min($stock, 20, $item->quantity),
                    ]);
                }
            }

            $sessionCart->delete();
        }

        session()->forget('cart_token');
    }

    public function owns(CartItem $item): void
    {
        abort_unless($item->cart_id === $this->current()->id, 403);
    }

    private function findExisting(): ?Cart
    {
        if ($user = auth()->user()) {
            return Cart::query()->where('user_id', $user->id)->first();
        }

        $token = session('cart_token');

        return $token ? Cart::query()->where('session_token', $token)->first() : null;
    }
}
