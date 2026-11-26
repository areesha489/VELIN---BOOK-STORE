<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Support\Pricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function place(User $user, Cart $cart, Address $address, PaymentMethod $payment): Order
    {
        return DB::transaction(function () use ($user, $cart, $address, $payment) {
            $items = $cart->items()->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            $lines = [];
            $subtotal = 0.0;

            foreach ($items as $item) {
                $book = Book::query()->whereKey($item->book_id)->lockForUpdate()->first();

                if (! $book || $book->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ($book->title ?? 'A title').' does not have enough copies left.',
                    ]);
                }

                $subtotal += (float) $book->price * $item->quantity;
                $lines[] = [$book, $item];
            }

            $shipping = Pricing::shipping($subtotal);
            $order = Order::create([
                'user_id' => $user->id,
                'number' => $this->number(),
                'status' => 'placed',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
                'address_snapshot' => [
                    'recipient' => $address->recipient,
                    'phone' => $address->phone,
                    'line1' => $address->line1,
                    'line2' => $address->line2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                ],
                'payment_snapshot' => [
                    'type' => $payment->type,
                    'brand' => $payment->brand,
                    'last_four' => $payment->last_four,
                    'upi_id' => $payment->upi_id,
                    'holder_name' => $payment->holder_name,
                ],
            ]);

            foreach ($lines as [$book, $item]) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'author_name' => $book->author()->value('name') ?? 'VELIN',
                    'price' => $book->price,
                    'quantity' => $item->quantity,
                ]);

                $book->decrement('stock', $item->quantity);
                $book->increment('popularity', $item->quantity);
            }

            $this->event($order, 'placed', 'Order received at the VELIN desk.');
            $cart->items()->delete();

            return $order;
        });
    }

    public function advance(Order $order, string $status, ?string $tracking, ?string $carrier, ?string $note): void
    {
        if (! array_key_exists($status, Order::STATUSES)) {
            throw ValidationException::withMessages([
                'status' => 'Choose a known order status.',
            ]);
        }

        $tracking = $tracking !== null && $tracking !== '' ? $tracking : $order->tracking_code;
        $carrier = $carrier !== null && $carrier !== '' ? $carrier : $order->carrier;

        if (in_array($status, ['shipped', 'out_for_delivery', 'delivered'], true) && ! $tracking) {
            throw ValidationException::withMessages([
                'tracking_code' => 'Add a tracking code before this parcel leaves the desk.',
            ]);
        }

        DB::transaction(function () use ($order, $status, $tracking, $carrier, $note) {
            $previous = $order->status;

            if ($previous !== 'cancelled' && $status === 'cancelled') {
                foreach ($order->items()->with('book')->get() as $item) {
                    $item->book?->increment('stock', $item->quantity);
                }
            }

            if ($previous === 'cancelled' && $status !== 'cancelled') {
                foreach ($order->items()->with('book')->lockForUpdate()->get() as $item) {
                    if (! $item->book || $item->book->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'status' => 'Not enough stock to reopen '.$item->title.'.',
                        ]);
                    }
                    $item->book->decrement('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => $status,
                'tracking_code' => $tracking,
                'carrier' => $carrier,
            ]);

            $message = $this->message($status);
            if ($note) {
                $message .= ' '.$note;
            }

            if ($previous !== $status || $note) {
                $this->event($order, $status, $message);
            }
        });
    }

    private function number(): string
    {
        do {
            $number = 'VL'.now()->format('ymd').strtoupper(Str::random(4));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }

    private function event(Order $order, string $status, string $message): void
    {
        OrderEvent::create([
            'order_id' => $order->id,
            'status' => $status,
            'message' => $message,
            'happened_at' => now(),
        ]);
    }

    private function message(string $status): string
    {
        return match ($status) {
            'placed' => 'Order received at the VELIN desk.',
            'confirmed' => 'The desk confirmed the copies.',
            'packed' => 'Packed in the bindery room.',
            'shipped' => 'Handed to the carrier.',
            'out_for_delivery' => 'Out for delivery.',
            'delivered' => 'Delivered.',
            'cancelled' => 'Cancelled. Copies returned to the stacks.',
            default => 'Order updated.',
        };
    }
}
