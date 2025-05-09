<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\cursor_tag_translation;

class cursor extends Model {

    protected $fillable = [
        'name',
        'name_en',
        'name_es',
        'cat',
        'offsetX',
        'offsetY',
        'offsetX_p',
        'offsetY_p',
        'schedule',
        'c_file',
        'p_file',
        'c_file_prev',
        'p_file_prev',
    ];

    
    public function categories() {
        return $this->belongsTo('App\Models\categories', 'cat');
    }

    public function tags()
    {
        return $this->hasMany(cursor_tag_translation::class, 'cursor_id');
    }

}
