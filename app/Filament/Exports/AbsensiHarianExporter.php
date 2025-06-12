<?php

namespace App\Filament\Exports;

use App\Models\AbsensiHarian;
use Illuminate\Support\Carbon;
use Filament\Actions\Exports\Exporter;
use Filament\Forms\Components\Builder;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class AbsensiHarianExporter extends Exporter
{
    protected static ?string $model = AbsensiHarian::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nip')
                ->label('NIP'),
            ExportColumn::make('user.name')
                ->label('Nama Karyawan'),
            ExportColumn::make('position')
                ->label('Jabatan'),
            ExportColumn::make('time_attendance')
                ->label('Tanggal Absen')
                ->getStateUsing(fn ($record) => $record->created_at ? Carbon::parse($record->created_at)->translatedFormat('d M Y') : '-'),
            ExportColumn::make('absenMasuk.time_attendance')
                ->label('Waktu Masuk')
                ->getStateUsing(fn ($record) => $record->absenMasuk?->time_attendance ? Carbon::parse($record->absenMasuk->time_attendance)->format('H:i:s') : '-'),
            ExportColumn::make('absenKeluar.time_attendance')
                ->label('Waktu Keluar')
                ->getStateUsing(fn ($record) => $record->absenKeluar?->time_attendance ? Carbon::parse($record->absenKeluar->time_attendance)->format('H:i:s') : '-'),
            ExportColumn::make('work_time')
                ->label('Durasi Waktu Kerja'),
            ExportColumn::make('absenMasuk.desc')
                ->label('Keterangan'),
            ExportColumn::make('lokasi_masuk')
                ->label('Lokasi Masuk')
                ->getStateUsing(fn ($record) => $record->absenMasuk?->latitude && $record->absenMasuk?->longitude 
                    ? "https://www.google.com/maps?q={$record->absenMasuk->latitude},{$record->absenMasuk->longitude}" 
                    : '-'),
            ExportColumn::make('lokasi_keluar')
                ->label('Lokasi Keluar')
                ->getStateUsing(fn ($record) => $record->absenKeluar?->latitude && $record->absenKeluar?->longitude 
                    ? "https://www.google.com/maps?q={$record->absenKeluar->latitude},{$record->absenKeluar->longitude}" 
                    : '-'),
            ExportColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn ($record) => $record->status == 0 ? 'Pending' : 'Approve'),
            ExportColumn::make('updatedBy.name')
                ->label('Di Approve Oleh'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your absensi harian export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
