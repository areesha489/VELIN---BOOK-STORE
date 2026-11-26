<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\PaymentMethodController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Store\BookController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CatalogController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\ReviewController;
use App\Http\Controllers\Store\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/guide', [PageController::class, 'guide'])->name('guide');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:12,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:6,1')->name('password.update');
    Route::post('/auth/{provider}/demo', [SocialAuthController::class, 'store'])->middleware('throttle:8,1')->name('social.demo');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');

    Route::get('/account', [ProfileController::class, 'edit'])->name('account.edit');
    Route::put('/account', [ProfileController::class, 'update'])->name('account.update');

    Route::get('/account/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    Route::get('/account/payments', [PaymentMethodController::class, 'index'])->name('payments.index');
    Route::post('/account/payments', [PaymentMethodController::class, 'store'])->name('payments.store');
    Route::patch('/account/payments/{paymentMethod}/default', [PaymentMethodController::class, 'default'])->name('payments.default');
    Route::delete('/account/payments/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payments.destroy');

    Route::get('/account/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/account/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');

    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
    Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');

    Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::post('/authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/readers', [UserController::class, 'index'])->name('users.index');
    Route::put('/readers/{user}', [UserController::class, 'update'])->name('users.update');
});
