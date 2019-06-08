@extends('layouts.app')

@section('content')


<div class="container">

<h1>Archived</h1>
    <div class="row">
        <div class="col-md-3">
            <div class="lis-group">
        @foreach( $thread  as $th)
        
       
       
        <a href="{{ url('conversation', $th['id']) }}" class="list-group-item">{{$th['subject']}}</a>
      
                <hr>
        @endforeach
        </div>
        
        </div> 
       
    </div>
</div>
@endsection
