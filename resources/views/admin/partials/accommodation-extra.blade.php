<fieldset>
    <legend>Fasilitas yang tersedia di unit ini</legend>
    @forelse ($facilities as $f)
        <label class="chk"><input type="checkbox" name="facility_ids[]" value="{{ $f->id }}" @checked(in_array($f->id, old('facility_ids', $selected)))> {{ $f->icon }} {{ $f->name }}</label>
    @empty <p class="muted">Belum ada fasilitas. Tambahkan dulu di menu Fasilitas.</p> @endforelse
</fieldset>
<fieldset>
    <legend>Galeri foto tambahan (maks 10 file per simpan, 3 MB per file)</legend>
    <input type="file" name="gallery_images[]" multiple accept="image/png,image/jpeg,image/webp">
    @if ($images->count())
        <div class="imggrid">
            @foreach ($images as $img)
                <label class="chk imgcell"><img src="{{ Media::url($img->path) }}" alt="Foto galeri" loading="lazy"><span><input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> Hapus</span></label>
            @endforeach
        </div>
    @endif
</fieldset>
