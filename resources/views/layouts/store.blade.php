<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10140f">
    <meta name="description" content="@yield('description', 'VELIN is a night bookstore from Mumbai. Search the stacks, keep a list, and track a parcel from the desk to your door.')">
    <title>{{ ($pageTitle = trim($__env->yieldContent('title'))) !== '' ? $pageTitle.' — VELIN' : 'VELIN — After-dark bookselling' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,620&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/velin.css') }}">
</head>
<body>
    <a class="skip" href="#main">Skip to content</a>
    @include('partials.nav')
    <main id="main">
        @include('partials.flash')
        @yield('content')
    </main>
    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
    <script src="{{ asset('js/velin.js') }}" defer></script>
</body>
</html>
