<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user:id,name,email')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($query) use ($term) {
                    $query->where('number', 'like', $term)
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $term)->orWhere('email', 'like', $term));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items', 'events']);

        return view('admin.orders.show', ['order' => $order]);
    }

    public function update(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
            'tracking_code' => ['nullable', 'string', 'max:40'],
            'carrier' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:240'],
        ]);

        $orders->advance(
            $order,
            $data['status'],
            $data['tracking_code'] ?? null,
            $data['carrier'] ?? null,
            $data['note'] ?? null,
        );

        return back()->with('status', 'Order '.$order->number.' updated.');
    }
}
