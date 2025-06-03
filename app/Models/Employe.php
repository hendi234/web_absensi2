<?php

namespace App\Models;

use App\Traits\AuditedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employe extends Model
{
    use HasFactory, AuditedBy, SoftDeletes;
    protected $table = 'employes';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id_employes');
    }
}
