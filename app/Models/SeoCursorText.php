<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use app\Models\Cursors;

class SeoCursorText extends Model
{
    use HasFactory;

    protected $fillable = [
        'cursor_id',
        'lang',
        'seo_title',
        'seo_description',
        'seo_page',
        'batch_id',
        'status',
        'error_message',
    ];

    // Зв'язок з моделлю Cursor
    public function seoTexts()
    {
        return $this->hasMany(SeoCursorText::class, 'cursor_id', 'id');
    }

    // Якщо треба одну для поточної мови:
    public function seoText($lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        return $this->hasOne(SeoCursorText::class, 'cursor_id', 'id')->where('lang', $lang);
    }

}