<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Books extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'author', 'publishing_year','cate_id'
    ];
}
