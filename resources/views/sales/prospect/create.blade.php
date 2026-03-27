@extends('layouts.app')

@section('content')
<div class="p-3">

    <!-- <h6 class="mb-3">Tambah Prospek</h6> -->

    <form method="POST" action="/prospect/store" enctype="multipart/form-data" id="prospect-form">
        @csrf

        <input type="text" name="name" class="form-control mb-2" placeholder="Nama Toko *" required autofocus>
        <input type="text" name="phone" class="form-control mb-2" placeholder="No HP">
        <input type="text" name="owner_name" class="form-control mb-2" placeholder="Owner / PIC">
        <textarea name="address" class="form-control mb-2" placeholder="Alamat" rows="2"></textarea>

        <!-- FOTO -->
        <div class="mb-3">
            <label>Foto Toko (max 3)</label>
            <div>
                <button type="button" id="open-camera" class="btn btn-primary w-100 mb-2">📸 Ambil Foto</button>
            </div>
            <div id="photo-preview" class="d-flex flex-wrap mt-2"></div>
        </div>

        <input type="hidden" name="gps_latitude" id="lat">
        <input type="hidden" name="gps_longitude" id="lng">

        <div class="mb-3">
            <button type="button" onclick="getLocation()" class="btn btn-outline-primary w-100">📍 Ambil Lokasi</button>
            <small id="gps-info" class="text-muted"></small>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">💾 Simpan</button>
            <a href="/prospect" class="btn btn-secondary flex-fill">✖ Batal</a>
        </div>
    </form>

</div>

<!-- MODAL FULLSCREEN CAMERA -->
<div id="camera-modal" style="display:none; position:fixed; inset:0; background:#000; z-index:1000; flex-direction:column; align-items:center; justify-content:flex-end; padding-bottom:30px;">
    <video id="cam-video" autoplay playsinline style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;"></video>

    <div style="position:relative; width:100%; display:flex; justify-content:center; align-items:center; z-index:10; gap:10px;">

        <!-- Switch camera di kiri tombol capture -->
        <button id="switch-camera" style="width:50px; height:50px; border:none; background:none; color:white; font-size:28px;">🔄</button>

        <!-- Capture -->
        <button id="take-photo" style="width:70px; height:70px; border-radius:50%; background:white; border:none;"></button>

        <!-- Mini preview -->
        <div id="mini-preview" style="display:flex; gap:5px; max-width:150px;"></div>

        <!-- Close modal -->
        <button id="close-camera" style="position:absolute; top:20px; left:20px; width:50px; height:50px; border-radius:50%; background:rgba(0,0,0,0.3); border:none; color:white; font-size:24px;">✖</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ===== GPS =====
function getLocation() {
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(pos){
            if(pos.coords.accuracy > 50){
                alert("GPS kurang akurat, aktifkan lokasi lebih presisi");
                return;
            }
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
            document.getElementById('gps-info').innerText = "Lokasi berhasil diambil ✔";
        });
    } else {
        alert("Browser tidak mendukung GPS");
    }
}

// ===== CAMERA MODAL =====
let cameraModal = document.getElementById('camera-modal');
let openCameraBtn = document.getElementById('open-camera');
let closeCameraBtn = document.getElementById('close-camera');
let video = document.getElementById('cam-video');
let takePhotoBtn = document.getElementById('take-photo');
let switchBtn = document.getElementById('switch-camera');
let miniPreview = document.getElementById('mini-preview');

let currentStream;
let usingFrontCamera = false;
let photos = [];

// Open modal
openCameraBtn.addEventListener('click', async () => {
    cameraModal.style.display = 'flex';
    await startCamera();
});

// Close modal
closeCameraBtn.addEventListener('click', () => {
    stopCamera();
    cameraModal.style.display = 'none';
});

// Start camera
async function startCamera() {
    if(currentStream){
        currentStream.getTracks().forEach(track => track.stop());
    }

    let constraints = { video: { facingMode: usingFrontCamera ? 'user' : 'environment' }, audio:false };

    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
    } catch(e){
        alert('Tidak bisa akses kamera: ' + e.message);
    }
}

// Stop camera
function stopCamera(){
    if(currentStream){
        currentStream.getTracks().forEach(track => track.stop());
    }
}

// Switch camera
switchBtn.addEventListener('click', async () => {
    usingFrontCamera = !usingFrontCamera;
    await startCamera();
});

// Ambil foto
takePhotoBtn.addEventListener('click', () => {
    if(photos.length >= 3){
        alert('Maksimal 3 foto');
        return;
    }

    let canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video,0,0,canvas.width,canvas.height);

    let dataUrl = canvas.toDataURL('image/jpeg',0.8);
    photos.push(dataUrl);

    // Preview utama di form
    let formPreview = document.getElementById('photo-preview');
    let img = document.createElement('img');
    img.src = dataUrl;
    img.style.width = '80px';
    img.style.height = '80px';
    img.style.objectFit = 'cover';
    img.style.marginRight = '5px';
    img.style.marginBottom = '5px';
    img.classList.add('rounded');
    formPreview.appendChild(img);

    // Hidden input
    let input = document.createElement('input');
    input.type='hidden';
    input.name='photos[]';
    input.value=dataUrl;
    document.getElementById('prospect-form').appendChild(input);

    // Mini preview di modal
    let mini = document.createElement('div');
    mini.style.position='relative';
    mini.style.width='50px';
    mini.style.height='50px';
    mini.style.flex='none';
    mini.style.borderRadius='6px';
    mini.style.overflow='hidden';

    let miniImg = document.createElement('img');
    miniImg.src = dataUrl;
    miniImg.style.width='100%';
    miniImg.style.height='100%';
    miniImg.style.objectFit='cover';
    mini.appendChild(miniImg);

    // Tombol hapus mini
    let delBtn = document.createElement('button');
    delBtn.innerText = '✖';
    delBtn.style.position='absolute';
    delBtn.style.top='0';
    delBtn.style.right='0';
    delBtn.style.background='rgba(255,0,0,0.7)';
    delBtn.style.color='white';
    delBtn.style.border='none';
    delBtn.style.borderRadius='0 6px 0 6px';
    delBtn.style.fontSize='12px';
    delBtn.style.padding='0 2px';
    delBtn.style.cursor='pointer';
    delBtn.addEventListener('click', ()=>{
        let index = photos.indexOf(dataUrl);
        if(index > -1) photos.splice(index,1);
        miniPreview.removeChild(mini);
        formPreview.removeChild(img);
        document.querySelectorAll("input[name='photos[]']").forEach(el=>{
            if(el.value===dataUrl) el.remove();
        });
        takePhotoBtn.disabled = false;
    });

    mini.appendChild(delBtn);
    miniPreview.appendChild(mini);

    // Disable capture jika sudah max
    if(photos.length >= 3){
        takePhotoBtn.disabled = true;
    }
});
</script>
@endsection