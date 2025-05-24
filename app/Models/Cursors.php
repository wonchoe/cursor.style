<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CursorTagTranslation;
use App\Models\CursorTranslation;
use App\Models\Collection;
use App\Models\SeoCursorText;

class Cursors extends Model {

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

    public function Collection() {
        return $this->belongsTo(Collection::class, 'cat');
    }

    public function tags()
    {
        return $this->hasMany(CursorTagTranslation::class, 'cursor_id');
    }

    public function translations()
    {
        return $this->hasMany(CursorTranslation::class, 'cursor_id');
    }

    public function currentTranslation()
    {
        return $this->hasOne(CursorTranslation::class, 'cursor_id')
                    ->where('lang', app()->getLocale());
    }

    public function getCursorUrlAttribute(): string
    {
        return asset_cdn($this->c_file_no_ext);
    }

    public function getPointerUrlAttribute(): string
    {
        return asset_cdn($this->p_file_no_ext);
    }

    public function seoTexts()
    {
        return $this->hasMany(SeoCursorText::class, 'cursor_id');
    }

}
