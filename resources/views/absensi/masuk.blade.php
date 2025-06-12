<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link rel="icon" href="images/artur.png" type="image/png" sizes="16x16">
    <title>Absen Masuk</title>
</head>
<body class="bg-gray-200">

@php
    use Filament\Facades\Filament;
    $user = Filament::auth()->user();
@endphp

<div class="max-w-[420px] h-auto shadow-lg border-2 border-gray-200 rounded-md bg-white mx-auto">
    <!-- Header User -->
    <div class="px-6 h-[220px] bg-gradient-to-r from-emerald-900 to-emerald-700 rounded-b-3xl text-white flex items-center justify-between border-b-8 border-amber-500">
        <div>
            <h2 class="py-1 font-bold text-lg">{{ $user->name }}</h2>
            <h2 class="text-[16px]">{{ $user->employe->position }}</h2>
        </div>
        <img src="{{ Storage::disk('karyawan')->url($user->avatar_url) }}" class="w-16 h-16 rounded-full object-cover border border-gray-100" alt="User Image" />
    </div>

    <!-- Tanggal dan Judul -->
    <div class="relative bg-gradient-to-br from-white to-gray-100 text-black max-w-[380px] h-auto mx-auto -mt-7 p-4 rounded-2xl shadow-lg border border-gray-200 text-center">
        <img src="{{ asset('images/iconabsen.png') }}" class="w-10 h-10 mx-auto" alt="Icon Absen" />
        <h1 class="text-xl font-semibold">Absen Masuk</h1>
        <h2 class="text-lg">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</h2>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded-md m-4">
        <strong>Ada kesalahan:</strong>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Absen -->
    <form action="{{ route('absenmasuk.absenMasuk') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Ambil Lokasi -->
        <div class="bg-gradient-to-br from-white to-gray-100 text-black max-w-[380px] mx-auto my-6 p-4 rounded-2xl shadow border border-gray-200 text-center">
            <img src="{{ asset('images/iconloc.png') }}" class="w-12 h-12 mx-auto mb-2" alt="Icon Lokasi" />
            <h1 class="text-xl font-semibold mb-2">Ambil Lokasi Anda</h1>
            <button type="button" id="getLocation" class="bg-emerald-800 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Dapatkan Lokasi</button>
            <div id="notification" class="hidden mt-2 p-2 bg-red-500 text-white rounded"></div>
        </div>

        <!-- Hidden Inputs -->
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
        <input type="hidden" name="foto" id="fotoInput" value="{{ old('fotoInput') }}">
        <input type="hidden" name="time_attendance" id="time_attendance" value="{{ \Carbon\Carbon::now() }}">


        <!-- Ambil Foto -->
        <div class="bg-gradient-to-br from-white to-gray-100 text-black max-w-[380px] mx-auto my-6 p-4 rounded-2xl shadow border border-gray-200">
            <label for="camera" class="block text-xl font-semibold text-center mb-2">Ambil Foto</label>
            <video id="video" class="w-full border border-gray-300 rounded-lg" autoplay></video>
            <div class="flex justify-center mt-4">
                <button type="button" id="capture" class="bg-emerald-800 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Ambil Foto</button>
            </div>
            <canvas id="canvas" class="hidden"></canvas>
            <img id="photo" class="mt-4 hidden w-full border border-gray-300 rounded-lg" alt="Hasil Foto">
        </div>

        <!-- Keterangan -->
        <div class="p-4">
            <label for="desc" class="block text-xl font-semibold text-center mb-2">Keterangan</label>
            <textarea name="desc" id="desc" rows="4" class="w-full p-2.5 text-sm bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan detail kebutuhanmu...">{{ old('desc') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center items-center py-4">
            <button type="submit" class="bg-emerald-800 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Absen Masuk</button>
        </div>
    </form>
</div>

<script>
    // Ambil Lokasi
    document.getElementById('getLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;

                   // Tampilkan pesan sukses
                let notif = document.getElementById('notification');
                notif.innerText = `✅ Data Lokasi berhasil diambil`;
                notif.classList.remove('hidden', 'bg-red-500');
                notif.classList.add('bg-amber-500');
            }, function() {
                let notif = document.getElementById('notification');
                notif.innerText = `❎ Mohon nyalakan lokasi Anda`;
                notif.classList.remove('hidden');
                notif.classList.add('bg-red-500');
            });
        } else {
            let notif = document.getElementById('notification');
            notif.innerText = '❎ Geolokasi tidak didukung oleh browser Anda.';
            notif.classList.remove('hidden');
            notif.classList.add('bg-red-500');
        }
    });

    // Kamera
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const photo = document.getElementById('photo');
    const captureButton = document.getElementById('capture');

    const constraints = {
        video: { facingMode: "environment" }
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => video.srcObject = stream)
        .catch(error => console.error("Error accessing the camera:", error));

    captureButton.addEventListener('click', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataURL = canvas.toDataURL('image/png');
        photo.src = dataURL;
        photo.classList.remove('hidden');
        document.getElementById('fotoInput').value = dataURL;
    });
</script>
</body>
</html>
