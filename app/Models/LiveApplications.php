<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveApplications extends Model
{

    use HasFactory;
    public $table = "live_application";

    function user(){
        return $this->hasOne(Users::class, 'id','user_id');
    }

}
