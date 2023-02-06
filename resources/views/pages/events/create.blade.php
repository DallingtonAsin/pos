@extends('layouts.master')


@section('content')

    <div class="panel panel-default">
      <div class="panel-heading">
       <div class="panel-title nunito-font">

        <div class="row">

          <div class="col-lg-2">
           <i class="fa fa-calendar roboto pr-2"></i> 
           <strong>Add new event</strong>
         </div>
      </div>
    </div>
  </div>

  <div class="panel-body">
        <form method="POST" action="{{ Route('events.store') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group mt-3">
          <div class="row">
           <div class="col-lg-4">
             <span>Title</span>
             <input type="text" name="event-title" class="form-control" 
             placeholder="Title of the Event" value="{{ old('event-title') }}" required autofocus="" autocomplete="off">
           </div>


           <div class="col-lg-3">
           <span>Start Date</span>
           <input type="date" name="eventStart-date" class="form-control"
           placeholder="Start Date of the Event" value="{{ old('eventStart-date') }}" required autofocus>
         </div>

         <div class="col-lg-3">
           <span>End Date</span>
           <input type="date" name="eventEnd-date" class="form-control"
           placeholder="End Date of the Event" value="{{ old('eventEnd-date') }}">
         </div>

         <div class="col-lg-2">
           <span>Start Time</span>
           <input type="text" name="event-time" class="event-time form-control"
           placeholder="Start time of the Event" value="{{ old('event-time') }}" required autofocus="">
         </div>

         </div>
       </div>


     <div class="form-group">
      <textarea id="event-message" name="event-message" rows="5" 
       class="form-control" required autofocus="" placeholder="Write your message..">{{ old('event-message') }}</textarea>
    </div>


    <div class="form-group">

      <div class="row">

       <div class="col-lg-2">
         <input type="submit" class="btn btn-success" name="submit" value="Submit">
       </div>
       <div class="col-lg-9 text-center">
          @if(session()->get('success'))
          <div class='alert alert-success alert-dismissible' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Yello!</strong> {{ session()->get('success') }}
             <i class="fa fa-check-circle"></i>
          </div>
          @endif

          @if(session()->get('fail'))
          <div class='alert alert-danger alert-dismissible' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Sorry,</strong> {{ session()->get('fail') }}
          </div>
          @endif

        </div>
    </div>
  </div>
</form>
</div>
</div>

<script>
$(document).ready(function(){
  var config = {};
  config.placeholder = 'some value';
  CKEDITOR.replace('event-message', config);
$('.event-time').wickedpicker({
  twentyFour:true,
});
});
</script>


@endsection

