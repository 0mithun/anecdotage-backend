<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{

    protected $fillable = [
        'name'
    ];


    /**
     * A thread can have many tags
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }


    public function getNameAttribute(){
        $user = $this->users()->where('id','!=', auth()->id())->first();
        return $user->name;
    }

    public function getAvatarAttribute(){
        $user = $this->users()->where('id','!=', auth()->id())->first();
        return $user->photo_Url;
    }

    public function messages(){
        return $this->hasMany(ChatMessage::class);
    }

    public function getLastMessageAttribute(){
        return $this->messages()->latest()->first();
    }

}
