<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyRequest extends Model
{
    use HasFactory;

    public $table = "verification_request";

    public function user()
    {
        return $this->hasOne(Users::class, "id", 'user_id');
    }
}
