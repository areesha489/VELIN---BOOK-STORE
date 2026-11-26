<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\AddressBook;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentVault;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $carts): View|RedirectResponse
    {
        $cart = $carts->current()->load('items.book.author');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Add a book before checkout.');
        }

        $user = $request->user();

        return view('store.checkout', [
            'cart' => $cart,
            'totals' => $carts->totals($cart),
            'addresses' => $user->addresses()->orderByDesc('is_default')->get(),
            'payments' => $user->paymentMethods()->orderByDesc('is_default')->get(),
        ]);
    }

    public function store(
        Request $request,
        CartService $carts,
        AddressBook $addresses,
        PaymentVault $vault,
        OrderService $orders,
    ): RedirectResponse {
        $cardNumber = (string) $request->input('card_number', '');
        $request->merge([
            'card_number' => $request->filled('card_number') ? '[redacted]' : null,
        ]);

        $user = $request->user();
        $cart = $carts->current()->load('items.book');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $address = $request->filled('address_id')
            ? $user->addresses()->whereKey($request->input('address_id'))->first()
            : null;

        if ($request->filled('address_id') && ! $address) {
            return back()->withErrors(['address_id' => 'Choose one of your saved addresses.'])->withInput();
        }

        if (! $address) {
            $address = $addresses->store($user, $request->all(), $request->boolean('address_default'));
        }

        $payment = $request->filled('payment_method_id')
            ? $user->paymentMethods()->whereKey($request->input('payment_method_id'))->first()
            : null;

        if ($request->filled('payment_method_id') && ! $payment) {
            return back()->withErrors(['payment_method_id' => 'Choose one of your saved payment methods.'])->withInput();
        }

        if (! $payment) {
            $payment = $vault->store($user, [
                'type' => $request->input('type', 'card'),
                'card_number' => $cardNumber,
                'holder_name' => $request->input('holder_name'),
                'exp_month' => $request->input('exp_month'),
                'exp_year' => $request->input('exp_year'),
                'upi_id' => $request->input('upi_id'),
            ], $request->boolean('payment_default'));
        }

        $order = $orders->place($user, $cart, $address, $payment);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Order '.$order->number.' is in. You can follow it from here.');
    }
}
