<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ThreadFilter implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if(\auth()->check()){
            if(auth()->id() !==1){
                $privacy = auth()->user()->userprivacy;
                if($privacy->restricted_18 ==1){
                    $builder->where('user_id', auth()->user()->id)->orWhere('is_published', '=', 1);
                }else if($privacy->restricted_13 == 1){
                    $builder->where('user_id', auth()->user()->id)->orWhere(function($query){
                        $query->where('age_restriction', '!=', 18)->where('is_published', '=', 1);
                    });
                }else if($privacy->restricted_18 ==0){
                    $builder->where('user_id', auth()->user()->id)->orWhere(function($query){
                        $query->where('age_restriction', 0)->where('is_published', '=', 1);
                    });
                }
            }

            // if(\auth()->user()->id !=1){
            //     $builder->where('user_id', auth()->user()->id)->orWhere('is_published', '=', 1);
            // }
        }else{
            $builder->where('age_restriction', 0)->where('is_published', '=', 1);
        }

    }
}
