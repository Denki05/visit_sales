@extends('layouts.app')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="p-2">

    <!-- SEARCH -->
    <div class="mb-2 sticky-top bg-white pt-2 pb-2">
        <input type="text" id="search" class="form-control" placeholder="🔍 Cari toko / phone...">
    </div>

    <!-- HEADER INFO -->
    <!-- <div class="mb-2">
        <small class="text-muted">Data Prospek Hari Ini</small>
    </div> -->

    <!-- LIST -->
    <div class="row g-2" id="list">

        @forelse($data as $item)
        <div class="col-12 col-md-6 item">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-2">

                    <div class="fw-bold name">{{ $item->name }}</div>

                    <small class="phone d-block text-muted">
                        📞 {{ $item->phone ?? '-' }}
                    </small>

                    <small class="address d-block text-muted">
                        📍 {{ $item->address ?? '-' }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center mt-5">
            <small class="text-muted">Belum ada data hari ini</small>
        </div>
        @endforelse

    </div>

</div>

@endsection

@section('scripts')
<script>
document.getElementById('search').addEventListener('keyup', function() {
    let keyword = this.value.toLowerCase();

    document.querySelectorAll('.item').forEach(function(el) {

        let name = el.querySelector('.name').innerText.toLowerCase();
        let phone = el.querySelector('.phone').innerText.toLowerCase();

        if (name.includes(keyword) || phone.includes(keyword)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }

    });
});
</script>
@endsection