<footer class="site-footer">
    <div class="wrap footer-grid">
        <div>
            <p class="logo"><span class="logo-mark" aria-hidden="true"></span>VELIN</p>
            <p>A night catalog from a reading room in Kala Ghoda. Edited short, shipped carefully, built for people who buy books after the office lights go out.</p>
        </div>
        <div>
            <p class="kicker">Visit</p>
            <p>18 Ropewalk Lane<br>Kala Ghoda, Mumbai 400001<br>Desk hours 10:00–19:00 IST</p>
        </div>
        <div>
            <p class="kicker">The room</p>
            <a href="{{ route('catalog.index') }}">All books</a>
            <a href="{{ route('guide') }}">Reader guide</a>
            <a href="{{ route('register') }}">Open an account</a>
            <a href="{{ route('cart.index') }}">Cart</a>
        </div>
        <div>
            <p class="kicker">Genres</p>
            @foreach(($navGenres ?? collect())->take(6) as $genre)
                <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}">{{ $genre->name }}</a>
            @endforeach
        </div>
    </div>
    <div class="wrap footer-base">
        <p>© {{ date('Y') }} VELIN. Prices in INR. GST included in the ticket price.</p>
        <p>Classroom build for the Aptech eProject.</p>
    </div>
</footer>
