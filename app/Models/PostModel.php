<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostModel extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'posts';


    public function get_media()
    {
        return $this->hasMany(MediaModel::class, 'post_id', 'id');
    }
}
