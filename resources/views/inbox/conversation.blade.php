@extends('layouts.app')

<style>
.mb-11 {
    margin-bottom: 11px;
}

</style>

@section('content')



<div class="container">
<h1>Conversation</h1>

<div class="row">
  <div class="col-md-2">
      <div class="card text-center">
            <p>Inbox</p>
            <p>Archived</p>
            <p>Delete</p>
      </div>
 </div>

 <div class="col-md-5">
  
   
        @if(!empty($threads))
          @foreach($threads as $thread)
  <div class="card text-center mb-11">
    <a href="{{url('talking',$thread->id)}}">
          <h3>{{$thread->name}}</h3>  

           <p> {{$thread->subject}} </p>
          </div>
          </a>
          @endforeach
        @endif
    
 </div>
</div>

</div>

@endsection