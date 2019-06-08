@extends('layouts.app')

@section('content')
<style>
.center {
  margin: auto;
  width: 60%;
  border: 3px solid #73AD21;
  padding: 10px;
}
</style>
<div class="row">
 <div class="col-md-6  ">
       <a href="/allmessages"> <h2>Inbox</h2></a> 
       <a href="/allarchived"> <h2>Archived</h2></a>
       <a href="/alldelete"> <h2>Delete</h2></a>
    </div>
<div class="mx_auto col-md-6  offset-3">

    
       <form action="/create" method="post">
       @csrf
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" class="form-control" id="subject" placeholder="Enter Subject">
        </div>

        <div class="form-group">
            <label for="exampleInputPassword1">Message</label>
            <textarea class="form-control" name="body" id="" cols="30" rows="10"></textarea>
        </div>
        @if($users->count() > 0)
                <div class="checkbox">
                    @foreach($users as $user)
                        @if($user->id != Auth::id())
                        <label title="{{ $user->name }}"><input type="checkbox" name="recipients[]"
                                                                value="{{ $user->id }}">{!!$user->name!!}</label>
                        @endif
                    @endforeach
                </div>
                <input type="hidden"  name="recipients[]" value="{{Auth::id()}}" >
            @endif
        <button type="submit" class="btn btn-primary">Submit</button>

       </form>
    </div>

   
</div>


@endsection
