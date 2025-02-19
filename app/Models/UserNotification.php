<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    public $table = "user_notification";

    public function user()
    {
        return $this->hasOne(Users::class, "id", 'my_user_id');
    }
}
