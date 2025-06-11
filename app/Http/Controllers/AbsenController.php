<?php

namespace App\Http\Controllers;

use App\Models\AbsenMasuk;
use App\Models\AbsenKeluar;
use Illuminate\Http\Request;
use App\Models\AbsensiHarian;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsenController extends Controller
{
    // Method untuk menampilkan halaman absen masuk
    public function create()
    {
        $absenMasuk = AbsenMasuk::all();
        return view('absensi.masuk', compact('absenMasuk'));
    }

    // Method untu get data user yang sedang login
    public function user()
    {
        $user = Filament::auth()->user();

        return view('absensi.masuk', compact('user'));
    }

    // Method untuk menyimpan data absen masuk
    public function absenMasuk(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
            'desc' => 'required|string|max:255',
            'time_attendance' => 'required|date',
        ], [
            'latitude.required' => 'Lokasi wajib di isi.',
            'longitude.required' => 'Lokasi wajib di isi.',
            'foto.required' => 'Foto wajib diunggah.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        $userId = $validated['user_id'];
        $today = Carbon::today();

        // Cek apakah user sudah absen masuk hari ini
        $existingAbsen = AbsenMasuk::where('user_id', $userId)
        ->whereDate('time_attendance', $today)
        ->exists();

        if ($existingAbsen) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        $validated['foto'] = $this->processImage($validated['foto']);

        // Set waktu absen
        $validated['time_attendance'] = Carbon::now()->locale('id');

        // Simpan ke database
        $absenMasuk = new AbsenMasuk();
        $absenMasuk->fill($validated);
        dd('$absenMasuk');
        $absenMasuk->save();

        return redirect('/absensi/absen-masuks')->with('success', 'Absen Masuk Berhasil');
    }

    // Menyimpan data absen keluar
    public function absenKeluar(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
        ], [
            'latitude.required' => 'Lokasi wajib di isi.',
            'longitude.required' => 'Lokasi wajib di isi.',
            'foto.required' => 'Foto wajib diunggah.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        // Cek apakah user sudah memiliki absen masuk hari ini
        $absenMasuk = AbsenMasuk::where('user_id', $request->user_id)
            ->whereDate('time_attendance', now()->toDateString())
            ->first();

        if (!$absenMasuk) {
            return redirect()->back()->with('error', 'Anda belum melakukan absen masuk hari ini!');
        }

        $userId = $validated['user_id'];
        $today = Carbon::today();

        // Cek apakah user sudah absen keluar hari ini
        $existingAbsenKeluar = AbsenKeluar::where('user_id', $userId)
            ->whereDate('time_attendance', $today)
            ->exists();

        if ($existingAbsenKeluar) {
            return back()->with('error', 'Anda sudah melakukan absen keluar hari ini.');
        }

        // Proses gambar
        $validated['foto'] = $this->processImage($validated['foto']);
        $validated['time_attendance'] = now();

        // Simpan Absen Keluar
        $absenKeluar = AbsenKeluar::create($validated);
            
        if ($absenMasuk) {
            // Hitung Durasi Kerja
            $masukTime = Carbon::parse($absenMasuk->time_attendance);
            $keluarTime = Carbon::parse($validated['time_attendance']);
            $durasiKerja = $keluarTime->diff($masukTime)->format('%H:%I:%S');

            // Cek apakah sudah ada AbsensiHarian untuk user ini hari ini
            $absensiHarian = AbsensiHarian::where('id_attendance_in', $absenMasuk->id)->first();

            if ($absensiHarian) {
                // Jika sudah ada, update data keluar & durasi
                $absensiHarian->update([
                    'id_attendance_out' => $absenKeluar->id,
                    'work_time' => $durasiKerja,
                    'status' => 0,
                ]);
            } else {
                // Jika belum ada, buat baru
                AbsensiHarian::create([
                    'tanggal' => now()->toDateString(),
                    'id_attendance_in' => $absenMasuk->id,
                    'id_attendance_out' => $absenKeluar->id,
                    'work_time' => $durasiKerja,
                    'status' => 0,
                    'updated_by' => null
                ]);
            }
        }

        return redirect('/absensi/absen-keluars')->with('success', 'Absen Keluar Berhasil');
    }

    // Method untuk memproses gambar
    private function processImage($base64Image)
    {
        $imageParts = explode(";base64,", $base64Image);
        if (count($imageParts) === 2) {
            $base64Image = $imageParts[1]; 
        }

        $imageData = base64_decode($base64Image);
        if ($imageData === false) {
            return back()->with('error', 'Gagal mengkonversi gambar.');
        }

        $imageName = uniqid() . '.png';
        Storage::disk('absensi')->put($imageName, $imageData);

        return "absensi_images/{$imageName}";
    }
}