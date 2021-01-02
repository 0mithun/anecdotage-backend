<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'content',
        'room_id',
        'sender_id',
        'username',
        'date',
        'timestamp',
        'system',
        'saved',
        'distributed',
        'seen',
        'disable_actions',
        'disable_reactions',
    ];

    protected $table = 'chat_messages';

    protected $casts = [
        'date'  => 'date',
        'timestamp'  => 'timestamp',
        'system'  => 'boolean',
        'saved'  => 'boolean',
        'seen'  => 'boolean',
        'disable_actions'  => 'boolean',
        'disable_reactions'  => 'boolean',
    ];

    public function childs(){
        return $this->hasMany(ChatMessage::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(ChatMessage::class,'parent_id');
    }
}
