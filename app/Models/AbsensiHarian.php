<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiHarian extends Model
{
    use HasFactory;
    protected $table = 'daily_attendance';
    protected $guarded = ['id'];

     // Menyimpan siapa yang terakhir mengupdate data
    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Auth::id(); // Ambil ID user yang sedang login
            }
        });
    }

    // Relasi ke User
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Getter untuk menampilkan nama user yang terakhir mengupdate
    public function getUpdatedNameAttribute()
    {
        return $this->updatedBy?->name ?? null ;
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, AbsenMasuk::class, 'id', 'id', 'id_absen_masuks', 'user_id');
    }    

    // Method untuk get data employe
    public function getPositionAttribute()
    {
        return Employe::select('employes.position')
            ->join('users', 'users.id_employes', '=', 'employes.id')
            ->join('absen_masuks', 'absen_masuks.user_id', '=', 'users.id')
            ->where('absen_masuks.id', $this->id_absen_masuks)
            ->value('position');
    }

    // Method untuk get data employe
    public function getNipAttribute()
    {
        return Employe::select('employes.nip')
            ->join('users', 'users.id_employes', '=', 'employes.id')
            ->join('absen_masuks', 'absen_masuks.user_id', '=', 'users.id')
            ->where('absen_masuks.id', $this->id_absen_masuks)
            ->value('nip');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'id_employes', 'id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'id_users', 'id');
    }

    public function absenMasuk()
    {
        return $this->belongsTo(AbsenMasuk::class, 'attendace_in');
    }

    public function absenKeluar()
    {
        return $this->belongsTo(AbsenKeluar::class, 'attendance_out');
    }
}
