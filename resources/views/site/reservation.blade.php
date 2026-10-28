@extends('layouts.public')
@section('title', 'Reservasi')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Pesan kamar</p><h1>Permintaan Reservasi</h1></div></section>
<section class="sec wrap narrow">
    <p>Isi formulir berikut. Permintaan Anda tersimpan dengan kode unik dan akan kami proses; reservasi baru dianggap terkonfirmasi setelah kami menghubungi Anda.</p>
    @include('partials.reserve-form', ['accommodations' => $accommodations, 'selected' => $selected])
</section>
@endsection
