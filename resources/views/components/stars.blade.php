@props(['rating' => null, 'count' => null])
@if($rating === null)
    <span class="stars muted">No ratings yet</span>
@else
    @php $rounded = (int) round((float) $rating); @endphp
    <span class="stars" aria-label="Rated {{ number_format((float) $rating, 1) }} out of 5">
        @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $rounded ? 'on' : 'off' }}" aria-hidden="true">★</span>
        @endfor
        <span class="stars-num">{{ number_format((float) $rating, 1) }}@if($count !== null) · {{ $count }}@endif</span>
    </span>
@endif
