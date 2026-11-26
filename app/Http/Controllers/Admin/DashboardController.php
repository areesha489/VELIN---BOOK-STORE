<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'bookCount' => Book::query()->count(),
            'readerCount' => User::query()->where('role', 'customer')->count(),
            'orderCount' => Order::query()->count(),
            'revenue' => (float) Order::query()->where('status', '!=', 'cancelled')->sum('total'),
            'openOrders' => Order::query()->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'recentOrders' => Order::query()->with('user:id,name')->latest()->take(6)->get(),
            'lowStock' => Book::query()->where('stock', '<=', 5)->orderBy('stock')->take(6)->get(),
        ]);
    }
}
