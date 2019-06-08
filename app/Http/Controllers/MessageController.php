<?php

namespace App\Http\Controllers;
use App\User;
use App\Thread;
use App\Message;
use App\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('message.index',compact('users'));
    }

     public function create(Request $request)
    {
        $input = $request->all();
      //  dd($input);
         
          $thread = Thread::create([
            'subject' => $input['subject'],
        ]);



        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $input['body'],
        ]);


     foreach ($input['recipients']  as $key ) {
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $key,        
            'unread' => ($key == Auth::id())? '0':'1',            
            'last_read' => new Carbon,
        ]);
        
        }
     
       
        return redirect()->back();
    }

    public function all()
    {
        $id= Auth::id();
        $thread = Thread::select('threads.*')
                            ->LeftjoinForParticipant()
                            ->where('participants.user_id' ,$id)
                            ->where('participants.status', "=", "Inbox")
                            ->get()
                            ->toArray();
                           // dd($thread);
                  return view('message.inbox',compact('thread'));
    }

    public function conversation($id)
    {
            $status = Participant::select('status')->where('user_id', Auth::id())->first();    
            $mesage = Message::select('messages.body','users.name','threads.id as thread_id' )
                              ->LeftjoinForUser()
                              ->LeftjoinForthread()
                              ->where('threads.id' ,'=', $id)
                              ->get()->toArray();
          //  dd($mesage);
             return view('message.conversation',compact('mesage','status'));
    }

    public function updates(Request $request, $id)
    {
           $input = $request->all();
            Message::create([
            'thread_id' =>$id,
            'user_id' => Auth::id(),
            'body' => $input['body'],
            ]);
                

        $participant = new Participant();

            
        $usr = Participant::where('thread_id','=', $id)->get();
        foreach($usr as $user)
        {
            if($user->user_id==auth()->user()->id)
            {
                $user->last_read =  date('Y-m-d');   
                $user->unread = 0;       
            }
            else
            {
                $user->unread = 1;     
            }
            $user->save();
                 
        }    

        return redirect()->back();
        }

        public function archived()
        {
                $id= Auth::id();
                $thread = Thread::select('threads.*')
                            ->LeftjoinForParticipant()
                            ->where('participants.user_id' ,$id)
                            ->where('participants.status', "=", "Archived")
                            ->get()
                            ->toArray();
                  return view('message.archived',compact('thread'));
        }

        public function delete()
        {
               $id= Auth::id();
               
              
                $thread = Thread::select('threads.*')
                            ->LeftjoinForParticipant()
                            ->where('participants.user_id' ,$id)
                            ->where('participants.status', "=", "Delete")
                            ->where('participants.soft_delete','=', null)
                            ->get()
                            ->toArray();
                           // dd($thread);
                  return view('message.delete',compact('thread'));
        }
        
    public function inboxStatus( $id)
    {
        $inbox = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Inbox',
                            ]);
                        
       return redirect()->back();      
    }

    public function ArchivedStatus( $id)
    {
        $archived = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Archived',
                            ]);
       return redirect()->back();      
    }
    public function DeleteStatus( $id)
    {
        $delete = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Delete',
                            ]);                             
       return redirect()->back();      
    }
    

    public function softDelete($id)
        {
            $delete = Participant::where('thread_id','=', $id)
                                ->where('user_id','=', auth()->user()->id)
                                ->update([
                                    'soft_delete' => 1,
                                ]);                             
            return redirect()->back(); 
        }
}
