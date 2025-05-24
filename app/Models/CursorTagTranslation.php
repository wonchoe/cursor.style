<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cursors;

class CursorTagTranslation extends Model
{
    protected $fillable = [
        'cursor_id',
        'lang',
        'tags',
    ];

    public function cursor()
    {
        return $this->belongsTo(Cursors::class, 'cursor_id');
    }
        
}
