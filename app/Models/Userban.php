<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Userban extends Model
{
    protected $fillable = [
        'user_id',
        'ban_type',
        'ban_reason',
        'ban_expire_on',
    ];

    protected $dates = ['ban_expire_on'];


    public function user(){
        return $this->belongsTo(User::class);
    }

}
