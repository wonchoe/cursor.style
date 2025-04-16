<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cursor extends Model {

    public function categories() {
        return $this->belongsTo('App\Models\categories', 'cat');
    }

}
