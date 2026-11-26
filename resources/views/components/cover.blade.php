@php
    $palette = \App\Models\Book::COVERS[$book->cover_key] ?? \App\Models\Book::COVERS['ink'];
    $label = 'Cover of '.$book->title.($book->author ? ' by '.$book->author->name : '');
    $words = preg_split('/\s+/', $book->title) ?: [$book->title];
    $lines = [];
    $current = '';
    foreach ($words as $word) {
        $next = trim($current.' '.$word);
        if (strlen($next) > 12 && $current !== '') {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $next;
        }
    }
    if ($current !== '') {
        $lines[] = $current;
    }
    $lines = array_slice($lines, 0, 6);
@endphp
<div class="cover-shell">
    @if($book->cover_path)
        <img class="cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="{{ $label }}">
    @else
        <svg class="cover" viewBox="0 0 300 440" role="img" aria-label="{{ $label }}">
            <rect width="300" height="440" fill="{{ $palette[0] }}"/>
            <rect x="250" y="28" width="22" height="22" fill="{{ $palette[1] }}"/>
            <text x="28" y="150" fill="{{ $palette[2] }}" font-size="26" font-family="Georgia, serif">
                @foreach($lines as $index => $line)
                    <tspan x="28" dy="{{ $index === 0 ? 0 : 32 }}">{{ $line }}</tspan>
                @endforeach
            </text>
            <text x="28" y="400" fill="{{ $palette[1] }}" font-size="13" font-family="Outfit, sans-serif">{{ \Illuminate\Support\Str::limit($book->author->name ?? 'VELIN', 28) }}</text>
        </svg>
    @endif
</div>
