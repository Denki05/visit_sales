@extends('layouts.app')

@section('content')

<div class="p-3">

    <h6 class="mb-3">Tambah Prospek</h6>

    <form method="POST" action="/prospect/store" enctype="multipart/form-data">
        @csrf

        <!-- NAMA -->
        <div class="mb-2">
            <input type="text" name="name" class="form-control"
                placeholder="Nama Toko *" required autofocus>
        </div>

        <!-- PHONE -->
        <div class="mb-2">
            <input type="text" name="phone" class="form-control"
                placeholder="No HP">
        </div>

        <!-- OWNER -->
        <div class="mb-2">
            <input type="text" name="owner_name" class="form-control"
                placeholder="Owner / PIC">
        </div>

        <!-- ADDRESS -->
        <div class="mb-2">
            <textarea name="address" class="form-control"
                placeholder="Alamat" rows="2"></textarea>
        </div>

        <!-- FOTO -->
        <div class="mb-3">
            <label>Foto Toko</label>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" capture="environment">
        </div>

        <!-- GPS -->
        <input type="hidden" name="gps_latitude" id="lat">
        <input type="hidden" name="gps_longitude" id="lng">

        <div class="mb-3">
            <button type="button" onclick="getLocation()" class="btn btn-outline-primary w-100">
                📍 Ambil Lokasi
            </button>

            <small id="gps-info" class="text-muted"></small>
        </div>

        <!-- SUBMIT -->
        <button class="btn btn-primary w-100">
            💾 Simpan
        </button>

    </form>

</div>

@endsection

@section('scripts')
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {

            // VALIDASI AKURASI
            if (position.coords.accuracy > 50) {
                alert("GPS kurang akurat, coba aktifkan lokasi lebih presisi");
                return;
            }

            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;

            document.getElementById('gps-info').innerHTML =
                "Lokasi berhasil diambil ✔";

        });
    } else {
        alert("Browser tidak mendukung GPS");
    }
}
</script>
@endsection