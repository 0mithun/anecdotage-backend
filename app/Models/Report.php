<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable  =[
        'user_id',
        'reason',
        'report_type',
        'reported_id',
        'reported_type',
    ];


    /**
     * Fetch the model that was reported.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reported()
    {
        return $this->morphTo();
    }
}
