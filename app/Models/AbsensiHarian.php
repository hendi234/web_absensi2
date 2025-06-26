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

    // Event deleting untuk menghapus relasi otomatis
    protected static function booted()
    {
        static::deleting(function ($absensi) {
            if ($absensi->absenMasuk) {
                $absensi->absenMasuk->delete();
            }
            if ($absensi->absenKeluar) {
                $absensi->absenKeluar->delete();
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
        return $this->hasOneThrough(User::class, AbsenMasuk::class, 'id', 'id', 'id_attendance_in', 'user_id');
    }    

    // Method untuk get data employe
    public function getPositionAttribute()
    {
        return Employe::select('employes.position')
            ->join('users', 'users.id_employes', '=', 'employes.id')
            ->join('attendance_in', 'attendance_in.user_id', '=', 'users.id')
            ->where('attendance_in.id', $this->id_attendance_in)
            ->value('position');
    }

    // Method untuk get data employe
    public function getNipAttribute()
    {
        return Employe::select('employes.nip')
            ->join('users', 'users.id_employes', '=', 'employes.id')
            ->join('attendance_in', 'attendance_in.user_id', '=', 'users.id')
            ->where('attendance_in.id', $this->id_attendance_in)
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
        return $this->belongsTo(AbsenMasuk::class, 'id_attendance_in');
    }

    public function absenKeluar()
    {
        return $this->belongsTo(AbsenKeluar::class, 'id_attendance_out');
    }
}
