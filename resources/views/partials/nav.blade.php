<header class="site-header">
    <div class="marquee ticker" aria-hidden="true">
        <div class="marquee-track">
            @foreach([1, 2] as $copy)
                <p class="marquee-set">
                    @foreach(range(1, 4) as $repeat)
                        <span>Free dispatch over ₹999</span>
                        <span>Prices include GST</span>
                        <span>Mumbai reading room</span>
                    @endforeach
                </p>
            @endforeach
        </div>
    </div>
    <p class="sr-only">Free dispatch over ₹999. Prices include GST. Mumbai reading room.</p>
    <div class="nav wrap">
        <a class="logo" href="{{ route('home') }}"><span class="logo-mark" aria-hidden="true"></span>VELIN</a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>Menu</button>
        <nav id="site-nav" class="nav-panel" data-nav-panel aria-label="Shop">
            <a href="{{ route('catalog.index') }}" @if(request()->routeIs('catalog.index') && !request('collection')) aria-current="page" @endif>Stacks</a>
            <a href="{{ route('catalog.index', ['collection' => 'bestsellers']) }}">Bestsellers</a>
            <a href="{{ route('catalog.index', ['collection' => 'new']) }}">New arrivals</a>
            <a href="{{ route('guide') }}" @if(request()->routeIs('guide')) aria-current="page" @endif>Guide</a>
            <form class="nav-search" method="GET" action="{{ route('catalog.index') }}" role="search">
                <label class="sr-only" for="nav-q">Search books</label>
                <input id="nav-q" type="search" name="q" placeholder="Search the stacks" value="{{ request('q') }}">
            </form>
        </nav>
        <div class="nav-tools">
            @auth
                <a class="tool" href="{{ route('wishlist.index') }}">List</a>
                <a class="tool" href="{{ route('account.edit') }}">{{ strtok(auth()->user()->name, ' ') }}</a>
                @if(auth()->user()->isAdmin())
                    <a class="tool" href="{{ route('admin.dashboard') }}">Desk</a>
                @endif
            @else
                <a class="tool" href="{{ route('login') }}">Sign in</a>
            @endauth
            <a class="tool" href="{{ route('cart.index') }}">
                Cart
                @if(($cartCount ?? 0) > 0)
                    <span class="count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>
</header>
