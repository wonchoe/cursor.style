<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;
    protected $fillable = [
        'date', 
        'project',        
        'installs',
        'uninstalls',
        'users_total',
        'rating_value',
        'feedbacks_total',
        'overal_rank',
        'cat_rank',
        'extension_install',
        'extension_active',
        'extension_update',        
    ];    
}
