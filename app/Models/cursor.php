<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
