<?php

namespace App\Http\Controllers;
use App\User;
use App\Thread;
use App\Message;
use App\Participant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use  App\Http\Requests\MessageRequest;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
class InboxController extends Controller
{
    public function __construct()
    {
        $this->validation = new MessageRequest();
    }
    public function index()
    {
        $users = User::all();
        return view('inbox.index',compact('users'));
    }

    public function message(Request $request)
    {
        $inbox = $request->all();
       // dd($inbox);
        $validator = $this->validation->messageAdd($inbox);

        if($validator->fails())
        {
            $response=array();
            $response['code'] = 404;
            $response['message']=$validator->errors()->first();
            return response()->json($response);
        }

        $thread = new thread();
        $thread->subject = $inbox['subject'];
        $thread->save();
      
        $message = new Message();      
        $message->thread_id = $thread->id;
        $message->user_id = Auth::id();
        $message->body = $inbox['message'];
        if($request->hasFile('userfile') && !empty($inbox['userfile'])  )
        {
           $document=Storage::disk('public')->putFile('employee/inbox', $request->file('userfile'));
           $message->files = $document;
        }
        $message->save();

        $participant = new Participant();
        $participant->thread_id = $thread->id;
        $participant->user_id = Auth::id();
        $participant->last_read = new carbon;
        $participant->unread = 1;
        $participant->save();

        foreach ($inbox['recipients'] as $key ) {
        $participant = new Participant();
        $participant->thread_id = $thread->id;
        $participant->user_id = $key;
        $participant->last_read = new carbon;
        $participant->unread = 0;
        $participant->save();
        }





        $response=array();
        $response['code']=200;
        $response['message']='Message has been send successfully';
        return response()->json($response);
    }


    public function conversation()
    {
        return view('inbox.conversation');
    }


}
