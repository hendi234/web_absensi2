<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @vite('resources/css/app.css')
    <link rel="icon" href="images/artur.png" type="image/png" sizes="16x16" />
    <title>Absen Keluar</title>
</head>
<body class="bg-gray-200">

    @php
        use Filament\Facades\Filament;
        $user = Filament::auth()->user();
    @endphp

    <!-- Kartu Data User -->
    <div class="max-w-[420px] mx-auto bg-white border-2 border-gray-200 rounded-md shadow-lg">

        <!-- Header User -->
        <div class="px-6 h-[220px] bg-gradient-to-r from-emerald-900 to-emerald-700 text-white rounded-b-3xl flex items-center justify-between border-b-8 border-amber-500">
            <div class="font-poppins">
                <h2 class="py-1 font-bold text-lg">{{ $user->name }}</h2>
                <h2 class="text-[16px]">{{ $user->employe->position }}</h2>
            </div>
            <img src="{{ Storage::disk('karyawan')->url($user->avatar_url) }}" alt="User Image" class="w-16 h-16 object-cover rounded-full border border-gray-100" />
        </div>

        <!-- Card Judul Absen -->
        <div class="relative -mt-7 max-w-[380px] mx-auto p-4 bg-gradient-to-br from-white to-gray-100 rounded-2xl shadow-lg border border-gray-200 text-black text-center">
            <img src="{{ asset('images/iconabsen.png') }}" alt="Icon Absen" class="w-10 h-10 mx-auto" />
            <h1 class="text-xl font-semibold">Absen Keluar</h1>
            <h2 class="text-lg">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</h2>
        </div>

         <!-- Pesan Success atau Error -->
    @if (session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md max-w-[380px] mx-auto mt-4 text-center border border-green-300">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 text-red-800 p-4 rounded-md max-w-[380px] mx-auto mt-4 text-center border border-red-300">
        {{ session('error') }}
    </div>
@endif

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
        <form action="{{ route('absenkeluar.absenKeluar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Card Lokasi -->
            <div class="relative max-w-[380px] mx-auto my-6 p-4 bg-gradient-to-br from-white to-gray-100 rounded-2xl shadow border border-gray-200 text-center">
                <img src="{{ asset('images/iconloc.png') }}" alt="Icon Lokasi" class="w-12 h-12 mx-auto mt-2" />
                <h1 class="text-xl font-semibold py-2">Ambil Lokasi Anda</h1>
                <button type="button" id="getLocation" class="mt-2 bg-emerald-800 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-200 text-[14px]">
                    Dapatkan Lokasi
                </button>
                <div id="notification" class="hidden mt-2 p-2 bg-red-500 text-white rounded"></div>
            </div>

            <!-- Hidden Input Data -->
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
            <input type="hidden" name="foto" id="fotoInput" value="{{ old('fotoInput') }}">
            <input type="hidden" name="time_attendance" id="time_attendance" value="{{ \Carbon\Carbon::now()->locale('id') }}">

            <!-- Card Ambil Foto -->
            <div class="relative max-w-[380px] mx-auto my-6 p-4 bg-gradient-to-br from-white to-gray-100 rounded-2xl shadow border border-gray-200 text-center">
                <label for="camera" class="block mb-2 text-xl font-semibold text-gray-900">Ambil Foto</label>
                <video id="video" class="w-full border border-gray-300 rounded-lg" autoplay></video>
                <button type="button" id="capture" class="mt-4 bg-emerald-800 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-200 text-[16px]">
                    Ambil Foto
                </button>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="photo" class="hidden mt-4 w-full border border-gray-300 rounded-lg" alt="Captured Image">
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-center items-center py-4">
                <button type="submit" class="bg-emerald-800 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-200 text-[14px]">
                    Absen Keluar
                </button>
            </div>
        </form>
    </div>

    <!-- Script -->
    <script>
        // Ambil Lokasi
        document.getElementById('getLocation').addEventListener('click', function () {
            const notif = document.getElementById('notification');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;

                        notif.innerText = '✅ Data Lokasi berhasil diambil';
                        notif.classList.remove('hidden', 'bg-red-500');
                        notif.classList.add('bg-amber-500');
                    },
                    function () {
                        notif.innerText = '❎ Mohon nyalakan lokasi Anda';
                        notif.classList.remove('hidden', 'bg-green-500');
                        notif.classList.add('bg-red-500');
                    }
                );
            } else {
                notif.innerText = '❎ Geolokasi tidak didukung oleh browser Anda.';
                notif.classList.remove('hidden', 'bg-green-500');
                notif.classList.add('bg-red-500');
            }
        });

        // Kamera dan Foto
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const captureButton = document.getElementById('capture');

        const constraints = {
            video: { facingMode: "environment" } // Kamera belakang
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => {
                console.error("Error accessing the camera:", error);
            });

        captureButton.addEventListener('click', function () {
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
