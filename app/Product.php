<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'nama_product', 'deskripsi_product', 'harga_product', 'gambar_product'
    ];

}
