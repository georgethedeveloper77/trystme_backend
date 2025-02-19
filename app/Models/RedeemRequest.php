<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemRequest extends Model
{
    use HasFactory;
    public $table = "redeem_requests";

    function user(){
        return $this->hasOne(Users::class, 'id','user_id');
    }
}
