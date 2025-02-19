<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    public $table = "images";
    public $timestamps = false;

    function user(){
        return $this->hasOne(Users::class, 'id','user_id');
    }
}
