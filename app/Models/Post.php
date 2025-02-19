<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    public $table = "posts";

    public function content()
    {
        return $this->hasMany(PostContent::class, 'post_id', 'id');
    }
    
    public function user()
    {
        return $this->hasOne(Users::class, 'id','user_id');
    }

    


}
