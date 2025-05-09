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

    public function cursor()
    {
        return $this->belongsTo(cursor::class, 'cursor_id');
    }
        
}
