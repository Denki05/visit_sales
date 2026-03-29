@extends('layouts.app')

@section('content')

<style>
body {
    background: #f5f6fa;
}

/* SEARCH */
.search-box {
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 12px;
}

/* FILTER */
.filter-chip {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 10px;
    white-space: nowrap;
}

/* CARD */
.card-item {
    border-radius: 10px;
}

.card-body {
    padding: 8px !important;
}

/* TAP EFFECT */
.item {
    cursor: pointer;
}
.item:active {
    background: #f1f1f1;
}

/* DEFAULT (DESKTOP & TABLET) */
.modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
}

/* MOBILE MODE */
@media (max-width: 576px) {

    .modal-dialog {
        position: fixed;
        bottom: 0;
        margin: 0;
        width: 100%;
        max-width: 100%;
    }

    .modal-content {
        border-radius: 15px 15px 0 0;
    }

}

.modal-content {
    border-radius: 15px 15px 0 0;
}

/* DRAG INDICATOR */
.modal-content::before {
    content: '';
    width: 40px;
    height: 4px;
    background: #ccc;
    display: block;
    margin: 10px auto;
    border-radius: 2px;
}

@media (min-width: 577px) and (max-width: 991px) {
    .modal-dialog {
        max-width: 600px;
    }
}
</style>

<div class="p-2">

    <!-- SEARCH + FILTER -->
    <div class="sticky-top bg-white pb-2 pt-2">
        <div class="d-flex align-items-center gap-2">

            <input type="text" id="search"
                class="form-control search-box"
                placeholder="🔍 Cari..."
                style="flex:1; min-width:0;">

            <div class="d-flex gap-1">
                <button class="btn btn-primary filter-chip filter-btn active" data-filter="today">Hari Ini</button>
                <button class="btn btn-outline-primary filter-chip filter-btn" data-filter="all">Semua</button>
                <button class="btn btn-outline-primary filter-chip filter-btn" data-filter="with-photo">📷</button>
            </div>

        </div>
    </div>

    <!-- LIST -->
    <div class="row g-2 mt-1" id="list">

        @forelse($data as $item)
        <div class="col-12 item"
            data-name="{{ $item->name }}"
            data-phone="{{ $item->phone }}"
            data-address="{{ $item->address }}"
            data-kelurahan="{{ $item->text_kelurahan }}"
            data-kecamatan="{{ $item->text_kecamatan }}"
            data-kota="{{ $item->text_kota }}"
            data-provinsi="{{ $item->text_provinsi }}"
            data-date="{{ $item->created_at->format('Y-m-d') }}"
            data-lat="{{ $item->gps_latitude }}"
            data-lng="{{ $item->gps_longitude }}"
            data-photos='@json($item->photos->pluck("file"))'>

            <div class="card card-item shadow-sm border-0">
                <div class="card-body">

                    <div class="fw-bold name" style="font-size:14px;">
                        {{ $item->name }}
                    </div>

                    <div class="text-muted phone" style="font-size:12px;">
                        📞 {{ $item->phone ?? '-' }}
                    </div>

                    <div class="text-muted address" style="font-size:12px;">
                        📍 {{ $item->address }},
                        {{ $item->text_kecamatan }},
                        {{ $item->text_kota }}
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="text-center mt-5">
            <small class="text-muted">Belum ada data</small>
        </div>
        @endforelse

    </div>

    <!-- EMPTY -->
    <div id="emptyState" class="text-center mt-4 d-none">
        <p class="text-muted">Data tidak ditemukan</p>
    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-3" id="modalContent"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// SUCCESS
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: "{{ session('success') }}",
    timer: 5000,
    showConfirmButton: false
});
@endif

let currentFilter = 'today';

const searchInput = document.getElementById('search');
const items = document.querySelectorAll('.item');
const emptyState = document.getElementById('emptyState');

// FILTER
function filterData() {

    let keyword = searchInput.value.toLowerCase();
    let today = new Date().toISOString().slice(0, 10);

    let visible = 0;

    items.forEach(el => {

        let name = el.querySelector('.name').innerText.toLowerCase();
        let phone = el.querySelector('.phone').innerText.toLowerCase();
        let address = el.querySelector('.address').innerText.toLowerCase();

        let date = el.dataset.date;
        let photo = el.dataset.photo;

        let matchSearch =
            name.includes(keyword) ||
            phone.includes(keyword) ||
            address.includes(keyword);

        let matchFilter = true;

        if (currentFilter === 'today') {
            matchFilter = date === today;
        }

        if (currentFilter === 'with-photo') {
            matchFilter = photo == 1;
        }

        if (matchSearch && matchFilter) {
            el.style.display = '';
            visible++;
        } else {
            el.style.display = 'none';
        }

    });

    if (emptyState) {
        emptyState.classList.toggle('d-none', visible > 0);
    }
}

// SEARCH
let typingTimer;
searchInput.addEventListener('keyup', function() {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(filterData, 300);
});

// FILTER BUTTON
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {

        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('btn-primary','active');
            b.classList.add('btn-outline-primary');
        });

        this.classList.add('btn-primary','active');
        this.classList.remove('btn-outline-primary');

        currentFilter = this.dataset.filter;

        filterData();
    });
});

function formatWa(phone) {
    if (!phone) return '';

    phone = phone.replace(/[^0-9]/g, '');

    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }

    return phone;
}

// CLICK ITEM → MODAL
document.querySelectorAll('.item').forEach(el => {

    el.addEventListener('click', function() {

        let name = this.dataset.name || '-';
        let phone = this.dataset.phone || '-';
        let address = this.dataset.address || '-';
        let kel = this.dataset.kelurahan || '';
        let kec = this.dataset.kecamatan || '';
        let kota = this.dataset.kota || '';
        let prov = this.dataset.provinsi || '';
        
        let lat = this.dataset.lat;
        let lng = this.dataset.lng;

        let photos = this.dataset.photos ? JSON.parse(this.dataset.photos) : [];

        // 🔥 MAPS
        let mapsUrl = '';
        if (lat && lng && lat !== 'null' && lng !== 'null') {
            mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        } else {
            mapsUrl = `https://www.google.com/maps?q=${kec},${kota}`;
        }

        // 🔥 WHATSAPP
        let wa = formatWa(phone);

        // 🔥 FOTO CAROUSEL
        let photoHtml = '';

        if (photos.length > 0) {
            photoHtml = `
                <div id="carouselPhotos" class="carousel slide mb-3">
                    <div class="carousel-inner">

                        ${photos.map((p, i) => `
                            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                                <img src="/uploads/customer/${p}" 
                                     class="d-block w-100 rounded"
                                     style="max-height:200px; object-fit:cover;">
                            </div>
                        `).join('')}

                    </div>

                    ${photos.length > 1 ? `
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPhotos" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPhotos" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    ` : ''}

                </div>
            `;
        }

        let html = `
            ${photoHtml}

            <div class="text-center mb-3">
                <div style="font-size:16px; font-weight:600;">${name}</div>
            </div>

            <div class="mb-2">📞 ${phone}</div>

            <div class="mb-3">
                📍 ${address}
                <div style="font-size:12px; color:#666;">
                    ${kel}, ${kec}, ${kota}, ${prov}
                </div>
            </div>

            <div class="d-grid gap-2">

                ${wa ? `
                <a href="https://wa.me/${wa}" target="_blank" class="btn btn-success">
                    💬 WhatsApp
                </a>
                ` : ''}

                <a href="${mapsUrl}" target="_blank" class="btn btn-outline-primary">
                    📍 Maps
                </a>

                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>

            </div>
        `;

        document.getElementById('modalContent').innerHTML = html;

        let modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
    });

});

const modalEl = document.getElementById('modalDetail');

modalEl.addEventListener('hidden.bs.modal', function () {
    // pindahkan fokus ke search input
    if (searchInput) {
        searchInput.focus();
    }
});
</script>

@endsection