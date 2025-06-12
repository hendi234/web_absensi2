<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenKeluar extends Model
{
    use HasFactory;

    protected $table = 'attendance_out';
    
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'foto',
        'desc',
        'time_attendance',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
