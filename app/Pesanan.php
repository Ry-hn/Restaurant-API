<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'id_product', 'id_user', 'jumlah_pesan'
    ];
}
