@extends('layouts.app')

@section('content')



<div class="container">
<h1>Conversation</h1>
<div class="row">
   <div class="col-md-6">
      <div class="lis-group">
    
         <?php $thread_id = ''; ?>
         @foreach( $mesage  as $mes)
         <?php $thread_id= $mes['thread_id']; ?>
         {{ $mes['name']}}
         <p>{{$mes['body']}}</p>
         <hr>
         @endforeach
      </div>
   </div>

<div class="col-md-6">
   <h3>Move to</h3>
      <ul>
         <li><a href="/inbox/{{$thread_id}}">Inbox </a></li>
         <li><a href="/archived/{{$thread_id}}">Archived</a></li>        

         @if($status->status == "Delete")
          <li><a href="/soft_delete/{{$thread_id}}">Delete</a></li>
         @else        
         <li><a href="/delete/{{$thread_id}}">Delete</a></li>
         @endif  
              
      </ul>
   </div>
   
</div>
</div>
<div class="container">
<div class="row">
   <div  class="col-md-6">
      <h2>Add a new message</h2>
      <form action="{{ url('update', $thread_id) }}" method="post">
         {{ csrf_field() }}
         <!-- Message Form Input -->
         <div class="form-group">
            <textarea name="body" class="form-control">{{ old('message') }}</textarea>
         </div>
         <!-- Submit Form Input -->
         <div class="form-group">
            <button type="submit" class="btn btn-primary form-control">Submit</button>
         </div>
      </form>
   </div>
</div>
</div>

@endsection
