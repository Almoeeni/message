<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $table='threads';
    protected $guarded = ['id' ];
    
    public function scopeLeftjoinForUser($query)
    {
        return $query->leftjoin('users','users.id' ,'=','threads.users_id');
    }
    public function scopeLeftjoinForParticipant($query)
    {
        return $query->leftjoin('participants','participants.thread_id' ,'=','threads.id');
    }

    public function scopeLeftjoinForMessage($query)
    {
        return $query->leftjoin('messages','messages.thread_id' ,'=','threads.id');
    }

}
