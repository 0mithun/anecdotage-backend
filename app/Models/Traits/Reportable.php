<?php

namespace App\Models\Traits;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;

trait Reportable
{
    /**
     * Boot the trait.
     */
    protected static function bootReportable()
    {
        static::deleting(function ($model) {
            $model->reports->each->delete();
        });
    }

    /**
     * A reply can be favorited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Favorite the current reply.
     *
     * @return Model
     */
    public function report(array $data)
    {
        $attributes = ['user_id' => auth()->check() ?  auth()->id() : 1 ];

        if (! $this->reports()->where($attributes)->exists()) {
            return $this->reports()->create($attributes + $data);
        }
    }


    /**
     * Determine if the current reply has been favorited.
     *
     * @return boolean
     */
    public function isReported()    {
        return auth()->check() &&  (bool) $this->reports->where('user_id', auth()->id())->count();
    }

    /**
     * Fetch the favorited status as a property.
     *
     * @return bool
     */
    public function getIsReportedAttribute()
    {
        return $this->isReported();
    }

}
