<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveHistory extends Model
{
    use HasFactory;
    public $table = "live_history";

    function user(){
        return $this->hasOne(Users::class, 'id','user_id');
    }

}
