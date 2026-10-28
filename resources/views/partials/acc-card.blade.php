<article class="acc">
    <a href="{{ route('accommodation.show', $a->slug) }}" class="accimg"><img src="{{ Media::url($a->featured_image, $a->name) }}" alt="{{ $a->name }}" loading="lazy"></a>
    <div class="accbody">
        <h3><a href="{{ route('accommodation.show', $a->slug) }}">{{ $a->name }}</a></h3>
        <p class="meta">Hingga {{ $a->capacity }} tamu @if ($a->bed_description)· {{ $a->bed_description }}@endif</p>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $a->description), 120) }}</p>
        <div class="accfoot"><span class="price">{{ $a->price_per_night ? Media::rupiah($a->price_per_night) . ' / malam' : 'Hubungi kami' }}</span>
            <span><a class="btn sm ghost" href="{{ route('accommodation.show', $a->slug) }}">Detail</a> <a class="btn sm" href="{{ route('reservation', ['akomodasi' => $a->slug]) }}">Reservasi</a></span></div>
    </div>
</article>
