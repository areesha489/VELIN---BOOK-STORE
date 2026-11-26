<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4efe4">
    <title>@yield('title') — VELIN desk</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,620&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/velin.css') }}">
</head>
<body class="admin-body">
    <a class="skip" href="#main">Skip to content</a>
    <div class="admin-shell">
        <aside class="admin-side">
            <div class="admin-brand">
                <a class="logo" href="{{ route('admin.dashboard') }}"><span class="logo-mark" aria-hidden="true"></span>VELIN</a>
                <p class="admin-brand-tag">Editor desk</p>
            </div>
            <nav aria-label="Editor">
                <p class="nav-label">Catalog</p>
                <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Overview</a>
                <a href="{{ route('admin.books.index') }}" @class(['is-active' => request()->routeIs('admin.books.*')])>Books</a>
                <a href="{{ route('admin.genres.index') }}" @class(['is-active' => request()->routeIs('admin.genres.*')])>Genres</a>
                <a href="{{ route('admin.authors.index') }}" @class(['is-active' => request()->routeIs('admin.authors.*')])>Authors</a>
                <p class="nav-label">Floor</p>
                <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>Orders</a>
                <a href="{{ route('admin.users.index') }}" @class(['is-active' => request()->routeIs('admin.users.*')])>Readers</a>
                <p class="nav-label">Shop</p>
                <a href="{{ route('home') }}">Open the shop</a>
                <a href="{{ route('guide') }}">Reader guide</a>
            </nav>
            <div class="admin-foot">
                <div class="admin-editor">
                    <p class="admin-editor-role">Editor</p>
                    <p class="admin-editor-name"><strong>{{ auth()->user()->name }}</strong></p>
                    <p class="admin-editor-email">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-btn" type="submit">Sign out</button>
                </form>
            </div>
        </aside>
        <div class="admin-main" id="main">
            @include('partials.flash')
            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/velin.js') }}" defer></script>
</body>
</html>
