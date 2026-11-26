@php
    $flow = \App\Models\Order::FLOW;
    $current = array_search($order->status, $flow, true);
@endphp
<section class="panel" aria-labelledby="track-title">
    <h2 id="track-title">Delivery</h2>
    @if($order->tracking_code)
        <p class="tracking-code"><span>{{ $order->carrier ?: 'Carrier' }}</span> {{ $order->tracking_code }}</p>
        <p class="fine">Use this code with the carrier. VELIN keeps the status here as the desk updates it.</p>
    @else
        <p class="fine">A tracking code appears once the parcel leaves the desk.</p>
    @endif
    @if($order->status === 'cancelled')
        <p class="banner">This order was cancelled. Copies went back to the stacks.</p>
    @else
        <ol class="stepper">
            @foreach($flow as $step)
                @php
                    $stepIndex = array_search($step, $flow, true);
                    $state = $current !== false && $stepIndex < $current ? 'done' : ($stepIndex === $current ? 'now' : 'wait');
                @endphp
                <li class="step {{ $state }}">{{ \App\Models\Order::STATUSES[$step] }}</li>
            @endforeach
        </ol>
    @endif
    <ol class="events">
        @foreach($order->events as $event)
            <li>
                <time datetime="{{ $event->happened_at->toAtomString() }}">{{ $event->happened_at->timezone(config('app.timezone'))->format('d M Y, H:i') }}</time>
                <strong>{{ \App\Models\Order::STATUSES[$event->status] ?? $event->status }}</strong>
                <p>{{ $event->message }}</p>
            </li>
        @endforeach
    </ol>
</section>
