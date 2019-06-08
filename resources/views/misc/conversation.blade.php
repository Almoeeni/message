@extends('layouts.app')
@section('title')
inbox
@endsection
@section('styles')
<style>
    .rounded {
        border-radius: 500px !important;
    }
</style>
@endsection

@section('content')

@include('modules.inbox.partials.new_message')

<div class="row">
   @include('modules.inbox.partials.sidebar')
 <?php $thread_id = '' ?>

@php  
  $thread_id = $thread->id;
  $subject   = $thread->subject;
@endphp

    <div class="col-9 pl-0">
        <div class="card" style="padding: 10px 25px 15px 25px;">
            <div class="row">
                <div class="col-6">
                  <div>
               @foreach($participant as $part)
                  @if(!empty($part['employee_profile_picture']))             
               <span onclick="short_info({{$part['id']}})" class="hover-pointer"> <a class="link-info"
                                href="#noanchor"><img src="{{asset('/storage').'/'.$part['employee_profile_picture']}}" class="rounded pr-2"  title=" {{$part['first_name'] .' '. $part['last_name']}}" style="width: 70px; height: 60px;"></a></span>
                  @else
                  <span onclick="short_info({{$part['id']}})" class="hover-pointer"> <a class="link-info"
                                href="#noanchor"><img src="{{url('img').'/profile.png'}}" title=" {{$part['first_name'] .' '. $part['last_name']}}" style="width:60px"></a></span>
                  @endif
                </span>
                
               @endforeach
               </div>
                    <p class="card-title mt-3">{{$subject}}</p>
                </div>
                <div class="col-6 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Move to</button>
                        <div class="dropdown-menu" x-placement="bottom-start"
                            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
                            <a class="dropdown-item" href="{{ url('modules/inbox/inbox-status', $thread_id) }}">Inbox</a>
                            <a class="dropdown-item" href="{{ url('modules/inbox/archived-status', $thread_id) }}">Archived</a>
                        </div>
                    </div>        
                    
                    @if($status->status == "Delete")
                    <a href="{{ url('modules/inbox/soft-delete-status', $thread_id) }}" class="btn btn-danger">Delete</a>
                    @else        
                    <a href="{{ url('modules/inbox/delete-status', $thread_id) }}" class="btn btn-danger">Delete</a>
                    @endif                                    
                    
                </div>
            </div>                 
            <div class="row">
                <div class="col-12 mt-4">

                     @foreach($message as $ms)
                            
                    <div class="media">
                        <div class="media-left">
                            @if(!empty($ms['employee_profile_picture']))             
                           <img src="{{asset('/storage').'/'.$ms['employee_profile_picture']}}" class="rounded pr-2"   style="width: 60px; height: 55px;">
                               @else
                               <img src="{{url('img').'/profile.png'}}" class="pr-2" style="width:60px">
                               @endif
                        </div>
                       
                   
                        <div class="media-body">
                            <h4 class="media-heading">{{$ms['first_name'] .' '. $ms['last_name']}} <small class="float-right"><i>{{datetime_format($ms['created_at'])}}</i></small></h4>
                            <p>{{$ms['body']}}</p>  
                        @if(!empty($ms['files']))
                        <?php
                        $info = pathinfo($ms['files']);
                        $ext = $info['extension'];
                        //dd($ext);
                        ?>                        
                        <div class="col-lg-6">
                            <label><a href="{{asset('/storage').'/'.$ms['files']}}"><i class="la la-file fs-20 p-r-5"></i> Document.{{$ext}}</a></label>
                        </div>
                        @endif
                         </div>
                    </div>
                     <hr>
                     @endforeach
                    <form action="/modules/inbox/update-conversation/{{$thread_id}}" method="post" id="frm_conversation_add">
                    <div class="form-group pt-4">
                            <textarea rows="2" cols="5" class="form-control" placeholder="Write a Reply" name="message"></textarea>
                       </div>
                        <div class="form-group">                
                            <div class="custom-file">
                                <input type="file"  name="document_file" class="custom-file-input" id="cv_upload">
                                <label class="custom-file-label" name="document_file" for="cv_upload">Choose file</label>
                            </div>
                        </div>
                        <div class="">
                            <button class="btn btn-primary ">Send Reply</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>


@include('modules.inbox.partials.modal')

@endsection
@section('javascript')
<script type="text/javascript">
  $(document).ready(function () {
     $('.submit_on_enter').keydown(function(event) {
    if (event.keyCode == 13) {
      this.form.submit();
      return false;
    }
  });
     $('.submit_on_enter').keydown(function(event) {
    if (event.keyCode == 13) {
      this.form.submit();
      return false;
    }
  });

    $("#frm_conversation_add").submit(function(e){
      e.preventDefault();
			var formData = new FormData(this);
      var frm=$(this);
      $.ajax({
        url: frm.attr('action'),
        method: 'post',
        dataType: 'json',
        data: formData,
        contentType: false,
        processData: false,
        success:function(data)
        {
          if(data.code==200)
          {
            alert_success_redirect(data.message,'{{ url('modules/inbox/conversation', $thread_id) }}');
             // alert_success_redirect(data.message);
          }
          else
          {
            alert_error(data.message);
          }
        },
        error: function () {
          alert_error();
        }
      });
    });


    $("#frm_inbox_add").submit(function(e){
      e.preventDefault();
			var formData = new FormData(this);
      var frm=$(this);
      $.ajax({
        url: frm.attr('action'),
        method: 'post',
        dataType: 'json',
        data: formData,
        contentType: false,
        processData: false,
        success:function(data)
        {
          if(data.code==200)
          {
            alert_success_redirect(data.message,'{{ url('modules/inbox/employee-inbox') }}');
          }
          else
          {
            alert_error(data.message);
          }
        },
        error: function () {
          alert_error();
        }
      });
    });
  });
</script>
@endsection
