<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cursor_tag_translation extends Model
{
    protected $fillable = [
        'cursor_id',
        'lang',
        'tags',
    ];
}
