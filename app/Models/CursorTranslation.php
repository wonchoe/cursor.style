<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cursors;

class CursorTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['lang', 'cursor_id', 'name'];

    public function cursor()
    {
        return $this->belongsTo(Cursors::class);
    }

}