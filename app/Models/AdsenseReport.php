<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AdsenseReport extends Model
{
    protected $fillable = [
        'date',
        'estimated_earnings',
        'clicks',
        'impressions',
        'page_views',
        'impressions_rpm',
        'cost_per_click',
    ];

    //
}
