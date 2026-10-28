<form class="rform" method="post" action="{{ route('reservation.send') }}" novalidate>
    @csrf
    <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
    @if ($errors->any())<div class="alert err" role="alert"><strong>Mohon periksa isian Anda:</strong><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <label>Nama lengkap<input type="text" name="guest_name" value="{{ old('guest_name') }}" required autocomplete="name"></label>
    <div class="two">
        <label>Telepon / WhatsApp<input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel"></label>
        <label>Email (opsional)<input type="email" name="email" value="{{ old('email') }}" autocomplete="email"></label>
    </div>
    <label>Akomodasi
        <select name="accommodation_id" required>
            <option value="">— Pilih akomodasi —</option>
            @foreach ($accommodations as $a)<option value="{{ $a->id }}" @selected((string) old('accommodation_id', $selected ?? '') === (string) $a->id)>{{ $a->name }} (maks {{ $a->capacity }} tamu)</option>@endforeach
        </select>
    </label>
    <div class="three">
        <label>Check-in<input type="date" name="check_in" value="{{ old('check_in') }}" min="{{ date('Y-m-d') }}" required></label>
        <label>Check-out<input type="date" name="check_out" value="{{ old('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required></label>
        <label>Jumlah tamu<input type="number" name="guest_count" value="{{ old('guest_count', 2) }}" min="1" required></label>
    </div>
    <label>Catatan tambahan<textarea name="special_requests" rows="3">{{ old('special_requests') }}</textarea></label>
    <button class="btn lg" type="submit">Kirim Permintaan Reservasi</button>
    <p class="note">Ini adalah <strong>permintaan</strong> reservasi, bukan pemesanan yang sudah terkonfirmasi. Kami akan menghubungi Anda untuk konfirmasi.</p>
</form>
