<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
   public function cursor() {
            return $this->hasMany('App\Models\cursor');
        }
}
