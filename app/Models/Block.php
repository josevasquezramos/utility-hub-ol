<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['name', 'tags'];

    protected $casts = [
        'tags' => 'array',
    ];
}
