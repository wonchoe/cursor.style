<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class CollectionTranslation extends Model
{
    protected $table = 'collections_translations';

    protected $fillable = ['lang', 'collection_id', 'name', 'short_desc', 'desc'];

    public function category()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }


    public function currentTranslation()
    {
        return $this->hasOne(CollectionTranslation::class, 'collection_id')
            ->where('lang', app()->getLocale());
    }    
}
