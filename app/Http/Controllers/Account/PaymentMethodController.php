<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\PaymentVault;
use App\Support\Defaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.payments', [
            'methods' => $request->user()->paymentMethods()->orderByDesc('is_default')->get(),
        ]);
    }

    public function store(Request $request, PaymentVault $vault): RedirectResponse
    {
        $cardNumber = (string) $request->input('card_number', '');
        $request->merge([
            'card_number' => $request->filled('card_number') ? '[redacted]' : null,
        ]);

        $vault->store($request->user(), [
            'type' => $request->input('type', 'card'),
            'card_number' => $cardNumber,
            'holder_name' => $request->input('holder_name'),
            'exp_month' => $request->input('exp_month'),
            'exp_year' => $request->input('exp_year'),
            'upi_id' => $request->input('upi_id'),
        ], $request->boolean('is_default'));

        return back()->with('status', 'Payment method saved. Full card numbers are never stored.');
    }

    public function default(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_unless($paymentMethod->user_id === $request->user()->id, 403);
        Defaults::payment($request->user(), $paymentMethod, true);

        return back()->with('status', 'Default payment updated.');
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_unless($paymentMethod->user_id === $request->user()->id, 403);

        $wasDefault = $paymentMethod->is_default;
        $paymentMethod->delete();

        if ($wasDefault) {
            $next = $request->user()->paymentMethods()->first();
            if ($next) {
                Defaults::payment($request->user(), $next, true);
            }
        }

        return back()->with('status', 'Payment method removed.');
    }
}
