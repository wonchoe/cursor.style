<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTranslation extends Model
{
    protected $table = 'collections_translations';

    protected $fillable = ['lang', 'collection_id', 'name', 'short_desc', 'desc'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'collection_id');
    }
}
