<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlideCategory extends Model
{
    protected $table = 'slide_categories';

    protected $fillable = [
        'search_term',
        'display_text',
        'type',
    ];



    public function getRouteKey()
    {
        return 'search_term';
    }
}
