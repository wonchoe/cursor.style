<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursorTranslation extends Model
{
    protected $fillable = ['lang', 'cursor_id', 'name'];
}