<nav class="account-nav" aria-label="Account">
    <a href="{{ route('account.edit') }}" @class(['is-active' => request()->routeIs('account.edit', 'account.update')])>Profile</a>
    <a href="{{ route('addresses.index') }}" @class(['is-active' => request()->routeIs('addresses.*')])>Addresses</a>
    <a href="{{ route('payments.index') }}" @class(['is-active' => request()->routeIs('payments.*')])>Payments</a>
    <a href="{{ route('orders.index') }}" @class(['is-active' => request()->routeIs('orders.*')])>Orders</a>
    <a href="{{ route('wishlist.index') }}" @class(['is-active' => request()->routeIs('wishlist.*')])>Wishlist</a>
</nav>
