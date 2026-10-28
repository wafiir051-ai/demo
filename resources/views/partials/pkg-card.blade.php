<article class="pkg">
    <img src="{{ Media::url($p->featured_image, $p->name) }}" alt="{{ $p->name }}" loading="lazy">
    <div class="pkbody">
        <p class="eyebrow">{{ \App\Models\Package::TYPES[$p->package_type] ?? $p->package_type }}@if ($p->capacity) · hingga {{ $p->capacity }} orang @endif</p>
        <h3>{{ $p->name }}</h3>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $p->description), 120) }}</p>
        @if ($p->items->count())<ul class="ticks">@foreach ($p->items as $it)<li>{{ $it->label }}</li>@endforeach</ul>@endif
        @if ($p->terms)<p class="terms"><strong>Syarat:</strong> {{ $p->terms }}</p>@endif
        <p class="price">{{ $p->price ? Media::rupiah($p->price) : 'Hubungi kami' }}</p>
        @php $wa = $biz->whatsappUrl('Halo, saya ingin bertanya tentang ' . $p->name); @endphp
        <p>@if ($wa)<a class="btn sm wa" href="{{ $wa }}" target="_blank" rel="noopener">Tanya via WhatsApp</a> @endif<a class="btn sm" href="{{ route('contact') }}">Hubungi kami</a></p>
    </div>
</article>
