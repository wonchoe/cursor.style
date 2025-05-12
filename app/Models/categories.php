<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CollectionTranslation;
class categories extends Model
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
    public function cursor()
    {
        return $this->hasMany('App\Models\cursor');
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
