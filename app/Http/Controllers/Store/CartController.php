<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $carts): View
    {
        $cart = $carts->findForDisplay();
        $cart?->load(['items.book.author', 'items.book.genre']);

        return view('store.cart', [
            'cart' => $cart,
            'totals' => $cart ? $carts->totals($cart) : ['subtotal' => 0, 'shipping' => 0, 'total' => 0],
        ]);
    }

    public function store(Request $request, CartService $carts): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $book = Book::query()->findOrFail($data['book_id']);
        $message = $carts->add($book, (int) ($data['quantity'] ?? 1));

        return back()->with('status', $message);
    }

    public function update(Request $request, CartItem $item, CartService $carts): RedirectResponse
    {
        $carts->owns($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        if ($data['quantity'] < 1) {
            $item->delete();

            return back()->with('status', 'Removed from the cart.');
        }

        $carts->setQuantity($item, $data['quantity']);

        return back()->with('status', 'Quantity updated.');
    }

    public function destroy(CartItem $item, CartService $carts): RedirectResponse
    {
        $carts->owns($item);
        $item->delete();

        return back()->with('status', 'Removed from the cart.');
    }
}
