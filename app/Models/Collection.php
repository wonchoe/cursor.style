<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CollectionTranslation;
class Collection extends Model
{
    protected $fillable = [
        'base_name',
        'base_name_en',
        'base_name_es',
        'alt_name',
        'priority',
        'installed',
        'description',
        'short_descr',
        'img',
    ];
    
    protected $table = 'categories';

    public function cursor()
    {
        return $this->hasMany('App\Models\Cursors');
    }
    
    public function translations()
    {
        return $this->hasMany(CollectionTranslation::class, 'collection_id');
    }

    public function currentTranslation()
    {
        return $this->hasOne(CollectionTranslation::class, 'collection_id')
                    ->where('lang', app()->getLocale());
    }    
}
