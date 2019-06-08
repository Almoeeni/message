<?php

namespace App\Http\Controllers\Modules\EmployeesDirectories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\Modules\Thread;
use App\Models\Modules\Message;
use App\Models\Modules\Participant;
use App\Http\Requests\Modules\Inbox\FormInboxRequest;
use App\Http\Requests\Modules\Inbox\UpdateConversationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class EmployeeInboxController extends Controller
{
    public function __construct()
    {
        $this->validation = new UpdateConversationRequest();
    }
 
    public function index(Request $request)
    {
        $search = $request->all();
        $users = User::where('user_status','active')->get();
        $id= Auth::id();

        $thread = Participant::select('threads.*', 'users.first_name','users.last_name','users.id as user_id','employee_details.employee_profile_picture','participants.unread')
                   ->LeftjoinForThread()                    
                    ->leftJoin('users', 'threads.user_id','=','users.id')
                    ->leftJoin('employee_details', 'users.id','=','employee_details.user_id') 
                    ->where([['participants.user_id','=',$id],['participants.status','=','Inbox'],['participants.soft_delete','!=',1]]);
                 
        if(!empty($search))
        {                        
           $thread= $thread->where('threads.subject', "LIKE", "%".$search['search']."%");
        }                        
           $thread= $thread->orderBy('threads.id', 'desc')->get()->toArray();
     
        return view('modules.inbox.index',compact('users','thread'));
    }

    public function archivedThreads(Request $request)
    {
        $search = $request->all();
        $users = User::where('user_status','active')->get();
        $id= Auth::id();

        $thread = Participant::select('threads.*', 'users.first_name','users.last_name','employee_details.employee_profile_picture','participants.unread')
                    ->where([['participants.user_id',$id],['participants.status','=','Archived'],['participants.soft_delete','!=',1]])
                   ->LeftjoinForThread()
                   ->LeftjoinForUser();
        if(!empty($search))
        {                        
           $thread= $thread->where('threads.subject', "LIKE", "%".$search['search']."%");
        }    
        $thread= $thread->orderBy('threads.id', 'desc')->get()->toArray();
        
        return view('modules.inbox.archived',compact('users','thread'));
    }

    public function deleteThreads(Request $request)
    {
        $search = $request->all();
        $users = User::where('user_status','active')->get();
        $id= Auth::id();

        $thread = Participant::select('threads.*', 'users.first_name','users.last_name','employee_details.employee_profile_picture','participants.unread')
                    ->where([['participants.user_id',$id],['participants.status','=','Delete'],['participants.soft_delete','!=',1]])
                   ->LeftjoinForThread()
                   ->LeftjoinForUser();
        if(!empty($search))
        {                        
           $thread= $thread->where('threads.subject', "LIKE", "%".$search['search']."%");
        }    
        $thread= $thread->orderBy('threads.id', 'desc')->get()->toArray();
        return view('modules.inbox.delete',compact('users','thread'));
    }

    public function addInbox(Request $request)
    {
        $input = $request->all();
        $validator = $this->validation->formInbox($input);
        if($validator->fails())
        {
            $response=array();
            $response['code']=404;
            $response['message']=$validator->errors()->first();
            return response()->json($response);
        }

        $threads = new Thread();
        $threads->subject = $input['subject'];
        $threads->user_id = Auth::id();
        $threads->save();

        $message = new Message();
        $message->thread_id = $threads->id;
        $message->user_id = Auth::id();
        $message->body = $input['notes'];
        if($request->hasFile('document') && !empty($input['document'])  )
        {
           $document=Storage::disk('public')->putFile('employee/inbox', $request->file('document'));
           $message->files = $document;
        }
        $message->save();
       
           $participant = new Participant();
           $participant->thread_id = $threads->id;
           $participant->user_id = Auth::id();
           $participant->last_read = new carbon;
           $participant->unread = 0;
           $participant->save();

        foreach($input['users'] as $key)
        {
           $participant = new Participant();
           $participant->thread_id = $threads->id;
           $participant->user_id = $key;
           $participant->last_read = new carbon;
           $participant->unread = 1;
           $participant->save();
        }

        $response=array();
        $response['code']=200;
        $response['message']='Message has been send successfully';
        return response()->json($response);

    }

    public function conversation($id)
    {
 
        $users = User::where('user_status','active')->get();
        $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            return redirect('modules\inbox\employee-inbox');    
        
        }             
        $status=$status->first();   
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            return redirect('modules\inbox\employee-inbox');   
        }
                 Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update(['unread' => 0,
                                    ]);    
                  
        $participant  = Participant::select('users.id','users.first_name','users.last_name','employee_details.employee_profile_picture')
                                    ->where('participants.thread_id',$id )
                                    ->LeftjoinForUser()                                
                                    ->get();  
         
        $message = Message::select('messages.body', 'messages.files','employee_details.employee_profile_picture','messages.created_at' ,'users.first_name','users.last_name' )
                            ->LeftjoinForUser()                                                            
                            ->LeftJoinForEmployeeDetails()  
                            ->where('messages.thread_id' ,'=',$id)
                            ->get()
                            ->toArray();
        
            
        return view('modules.inbox.conversation',compact('message','status','users','thread','participant'));       
             
    }

    public function updateConversation(Request $request, $id)
    {
        $input = $request->all();
        $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            $response=array();
            $response['code']=404;
            $response['message']='Access denied';
            return response()->json($response);
        
        }   
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            $response=array();
            $response['code']=404;
            $response['message']='Access denied';
            return response()->json($response);  
        }       


        //dd($input);
        $validator = $this->validation->conversationUpdate($input);
        if($validator->fails())
        {
            $response=array();
            $response['code']=404;
            $response['message']=$validator->errors()->first();
            return response()->json($response);
        }

        $message = new Message();
        $message->thread_id = $id;
        $message->user_id = Auth::id();
        $message->body = $input['message'];
        if($request->hasFile('document_file') && !empty($input['document_file'])  )
        {
           $document=Storage::disk('public')->putFile('employee/inbox', $request->file('document_file'));
           $message->files = $document;
        }
        $message->save();

        $user = Participant::where('thread_id','=', $id)->get();
        foreach($user as $users)
        {
            if($users->user_id==auth()->user()->id)
            {
                $users->last_read =  date('Y-m-d');   
                $users->unread = 0;       
            }
            else
            {
                $users->unread = 1;     
            }
            $users->save();                 
        }  

        $response=array();
        $response['code']=200;
        $response['message']='Message has been send successfully';
        return response()->json($response);
    }    

    public function inboxStatus( $id)
    {

        $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            return redirect('modules\inbox\employee-inbox');    
        
        }                     
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            return redirect('modules\inbox\employee-inbox');   
        }


        $inbox = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Inbox',
                            ]);                        
       return redirect('modules\inbox\employee-inbox');      
    }

    public function ArchivedStatus( $id)
    {
         $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            return redirect('modules\inbox\employee-inbox');    
        
        }                     
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            return redirect('modules\inbox\employee-inbox');   
        }

        $archived = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Archived',
                            ]);                            
       return redirect('modules\inbox\archived-messages');       
    }

    public function DeleteStatus( $id)
    {
         $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            return redirect('modules\inbox\employee-inbox');    
        
        }                     
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            return redirect('modules\inbox\employee-inbox');   
        }

        $delete = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'status' => 'Delete',
                            ]);                             
       return redirect('modules\inbox\delete-messages');      
    } 

    public function softDelete($id)
    {
         $status = Participant::select('status')->where('user_id', Auth::id())
                 ->where('participants.thread_id' ,'=', $id)
                 ->where('participants.soft_delete' ,'!=', 1)
                 ->get();
        if($status->isEmpty())
        {
            return redirect('modules\inbox\employee-inbox');    
        
        }                     
        $thread = Thread::select('*')->where('id',$id)->get()->first();    
        if(!$thread)
        {
            return redirect('modules\inbox\employee-inbox');   
        }

        $delete = Participant::where('thread_id','=', $id)
                            ->where('user_id','=', auth()->user()->id)
                            ->update([
                                'soft_delete' => 1,
                            ]);                             
        return redirect('modules\inbox\delete-messages'); 
    }
}
