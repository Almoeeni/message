@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">
        <div  class="col-md-6">
            <form action="{{url('/addmessage')}}" id="inbox-message" method="post" enctype="multipart/form-data">
            @csrf
                <div class="form-group">       
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" name="subject" id="subject">            
                </div>
                <div class="form-group">       
                    <label for="message">Message</label>    
                    <textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea>
                </div>
                <div>
                    <label for="userfile">Upload file:</label>
                    <input type="file" id="userfile" name="userfile">
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
                
                @endif

                <button type="submit" class="btn btn-primary submit" > Submit</button>     
            </form>        
        </div>
    </div>
</div>


<script>
$(document).ready(function(){
   $("#inbox-message").submit(function(e){
       e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
   //console.log($("#userfile").val());
   //var file = $("#userfile").val();
   // var form = $("#inbox-message").serializeArray();
  var form = new FormData(this);
    // console.log(form);
    // return false;
    var frm = $(this);
 // console.log(form);
  //return false;
    $.ajax({
                url:frm.attr('action'),
           datatype: 'json',
             method: 'post',
               data:form,
        contentType: false,
        processData: false,
          success:function(data)
          {
              if(data.code == 200)
              {
                  alert(data.message);

              }
              else if(data.code == 404)
              {
                  alert(data.message);
              }
          },
    });
   
   });
});
</script>

@endsection

