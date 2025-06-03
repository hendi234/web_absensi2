<?php

namespace App\Filament\Widgets;

use App\Models\Employe;
use Illuminate\Support\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $karyawan = Employe::count();
        $tanggal = Carbon::now()->translatedFormat('d M Y');
        $jam = Carbon::now()->translatedFormat('H:i:s');

         // Stat ini selalu tampil untuk semua user
         if ($user->id_roles === 2) {
            $stats[] = Stat::make('Selamat Beraktivitas', $jam)
            ->description($tanggal)
            ->descriptionIcon('heroicon-m-calendar-days', IconPosition::Before)
                ->color('success')
                ->icon('heroicon-s-bell-alert') // Menambahkan ikon bell-alert
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }

        // Stat ini selalu tampil untuk admin
        if ($user->id_roles === 1) {
            $stats[] = Stat::make('Selamat Beraktivitas', $jam)
                ->color('success')
                ->icon('heroicon-s-bell-alert') // Menambahkan ikon bell-alert
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }
        
        
          // Stat ini selalu tampil untuk admin
          if ($user->id_roles === 1) {
            $stats[] = Stat::make('Tanggal', $tanggal)
                ->color('success')
                ->icon('heroicon-s-calendar') // Menambahkan ikon kalender
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }        

        // Hanya admin yang bisa melihat "Jumlah Karyawan"
        if ($user->id_roles === 1) {
            $stats[] = Stat::make('Jumlah Karyawan', $karyawan . ' Karyawan')
                ->color('success')
                ->icon('heroicon-s-users') // Menambahkan ikon user-group
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }
        

        // Hanya karyawan yang bisa melihat "Absensi Masuk" dan "Absensi Keluar"
        if ($user->id_roles === 2) {
            $stats[] = Stat::make('', 'Absen Masuk')
                ->description('Klik Untuk Absen Masuk')
                ->descriptionIcon('heroicon-m-user', IconPosition::Before) // Menambahkan ikon sebelum deskripsi
                ->extraAttributes([
                    'class' => 'cursor-pointer text-primary font-bold',
                    'onclick' => "window.location.href='/absenmasuk'",
                    'style' => 'background-color: #22c55e; color: white;'
                ])
                ->color('white')
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        

            $stats[] = Stat::make('', 'Absensi Keluar')
                ->description('Klik Untuk Absen Keluar')
                ->descriptionIcon('heroicon-m-user', IconPosition::Before) // Menambahkan ikon sebelum deskripsi
                ->extraAttributes([
                    'class' => 'cursor-pointer text-primary font-bold',
                    'onclick' => "window.location.href='/absenkeluar'",
                    'style' => 'background-color: #ef4444; color: white;'
                ])
                ->color('white')
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }

        return $stats;
    }
}
