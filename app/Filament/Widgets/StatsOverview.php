<?php

namespace App\Filament\Widgets;

use App\Models\Employe;
use Illuminate\Support\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\AbsensiHarian;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
{
    $user = Auth::user();
    $karyawan = Employe::count();
    $tanggal = Carbon::now()->translatedFormat('d M Y');
    $jam = Carbon::now()->translatedFormat('H:i:s');

    // ✅ FIX: Inisialisasi array
    $stats = [];

    // Keseluruhan role
    $stats[] = Stat::make('Selamat Beraktivitas', $jam)
    ->description($tanggal)
    ->descriptionIcon('heroicon-m-calendar-days', IconPosition::Before)
    ->color('success')
    ->icon('heroicon-s-bell-alert')
    ->chart([7, 2, 10, 3, 15, 4, 17]);

     // Stat khusus untuk Admin
     if ($user->id_roles === 1) {

        // Hitung absensi hari ini
        $absensiHarian = AbsensiHarian::whereDate('created_at', Carbon::today())->count();
    
        // Yang sudah di-ACC
        $sudahAcc = AbsensiHarian::where('status', 1)
            ->whereDate('created_at', Carbon::today())
            ->count();
    
        // Yang belum di-ACC
        $belumAcc = AbsensiHarian::where('status', 0)
            ->whereDate('created_at', Carbon::today())
            ->count();
    
             // Data jumlah karyawan
        $karyawan = User::where('id_roles', 2)->count();

        // Tampilkan stat
        $stats[] = Stat::make('Jumlah Karyawan', $karyawan . ' Karyawan')
            ->description('Total Data karyawan')
            ->color('success')
            ->icon('heroicon-s-users')
            ->chart([5, 10, 15, 20, $karyawan, 10, 5]);

        $stats[] = Stat::make('Rekap Absensi Harian', $absensiHarian . ' Data')
            ->description('Total rekap absensi harian')
            ->color('success')
            ->icon('heroicon-s-calendar-days')
            ->chart([3, 4, 6, 5, $absensiHarian, 7, 6]);
    
       $stats[] = Stat::make('Sudah ACC Hari Ini', $sudahAcc . ' Orang')
            ->description('Absensi yang sudah disetujui')
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->chart([$sudahAcc, 0, 1, 0, 2, 0, 1]);

        $stats[] = Stat::make('Belum ACC Hari Ini', $belumAcc . ' Orang')
            ->description('Menunggu persetujuan ACC')
            ->color('danger')
            ->icon('heroicon-s-exclamation-circle')
            ->chart([0, $belumAcc, 0, 0, 0, 0, 0]);

        // $kepalaDivisi = User::where('id_roles', 3)->count();

        // $stats[] = Stat::make('Kepala Divisi', $kepalaDivisi . ' Orang')
        // ->description('Jumlah Data pengguna')
        //     ->color('info')
        //     ->icon('heroicon-s-user-circle')
        //     ->chart([0, 0, $kepalaDivisi, 0, 0, 0, 0]);

    }
    

    // Tombol Absensi khusus karyawan
    if ($user->id_roles === 2) {
        $stats[] = Stat::make('', 'Absen Masuk')
            ->description('Klik Untuk Absen Masuk')
            ->descriptionIcon('heroicon-m-user', IconPosition::Before)
            ->extraAttributes([
                'class' => 'cursor-pointer text-primary font-bold',
                'onclick' => "window.location.href='/absenmasuk'",
                'style' => 'background-color: #22c55e; color: white;'
            ])
            ->color('white')
            ->chart([7, 2, 10, 3, 15, 4, 17]);

        $stats[] = Stat::make('', 'Absensi Keluar')
            ->description('Klik Untuk Absen Keluar')
            ->descriptionIcon('heroicon-m-user', IconPosition::Before)
            ->extraAttributes([
                'class' => 'cursor-pointer text-primary font-bold',
                'onclick' => "window.location.href='/absenkeluar'",
                'style' => 'background-color: #ef4444; color: white;'
            ])
            ->color('white')
            ->chart([7, 2, 10, 3, 15, 4, 17]);
    }

    if (!in_array($user->id_roles, [1, 2])) {
        // ✅ Total absensi yang sudah masuk hari ini
        $absenHariIni = AbsensiHarian::whereDate('created_at', Carbon::today())->count();
    
        $stats[] = Stat::make('Total Rekap Absensi Harian', $absenHariIni . ' Karyawan')
            ->description('Data Rekap absensi harian')
            ->descriptionIcon('heroicon-o-user-group')
            ->icon('heroicon-s-clipboard-document-check')
            ->color('success')
            ->chart([7, 5, 12, 4, 15, 6, 9]);
    
        // ✅ Total absensi yang belum di-ACC (status = 0)
        $belumAcc = AbsensiHarian::where('status', 0)
            ->whereDate('created_at', Carbon::today())
            ->count();
    
        $stats[] = Stat::make('Data Absen Belum Di-ACC', $belumAcc . ' Data')
            ->description('Menunggu persetujuan')
            ->descriptionIcon('heroicon-s-clock')
            ->icon('heroicon-s-exclamation-triangle')
            ->color('warning')
            ->chart([3, 4, 2, 5, 6, 2, 1]);
    }    
    
    return $stats;
    }
}
