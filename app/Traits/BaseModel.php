<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait BaseModel
{
    /**
     * Method untuk menambahkan kolom umum ke dalam tabel.
     */
    public function base(Blueprint $table)
    {
        $table->unsignedBigInteger('created_by')->default(1);
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
        $table->timestamps();
        $table->softDeletes();
    }
}
