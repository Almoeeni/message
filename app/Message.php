<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
      protected $table='messages';
      protected $guarded = ['id' ];
      
     public function scopeLeftjoinForUser($query)
    {
        return $query->leftjoin('users','users.id' ,'=','messages.user_id');
    }

      public function scopeLeftjoinForthread($query)
    {
        return $query->leftjoin('threads','threads.id' ,'=','messages.thread_id');
    }
}
