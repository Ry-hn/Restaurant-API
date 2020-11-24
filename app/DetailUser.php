<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailUser extends Model
{   
    public $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'email', 'nama_user', 'telepon',
    ];

    protected $guarded = [];  
}
