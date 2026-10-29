{{-- Pemilih wilayah bertingkat. $p = awalan id elemen (cart / pd). Skrip: public/js/region-picker.js --}}
<div class="form-group">
    <label class="form-label">Provinsi *</label>
    <select class="form-control" id="{{ $p }}Prov"><option value="">Memuat...</option></select>
</div>
<div class="form-group">
    <label class="form-label">Kabupaten / Kota *</label>
    <select class="form-control" id="{{ $p }}Kab" disabled><option value="">-- Pilih --</option></select>
</div>
<div class="form-group">
    <label class="form-label">Kecamatan *</label>
    <select class="form-control" id="{{ $p }}Kec" disabled><option value="">-- Pilih --</option></select>
</div>
<div class="form-group">
    <label class="form-label">Desa / Kelurahan *</label>
    <select class="form-control" id="{{ $p }}Desa" disabled><option value="">-- Pilih --</option></select>
</div>
<div id="{{ $p }}ZoneInfo" style="display:none;font-size:.85rem;font-weight:600;margin:-.25rem 0 .75rem;"></div>
<p id="{{ $p }}Note" style="display:none;font-size:.78rem;color:#b45309;margin:-.25rem 0 .75rem;">
    Daftar wilayah belum bisa dimuat. Tulis alamat lengkap (sampai kecamatan dan kota) di kolom di bawah.
</p>
