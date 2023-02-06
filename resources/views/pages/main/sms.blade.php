@extends('layouts.master')


@section('content')
{{-- <div class="card-table"> --}}
  <div class="card card-dashboard-table-six">
    <div class="card-title">
            <strong class="nunito-font">
             <i class="fa fa-envelope pr-2"></i>Send SMS
            </strong>
            </div> 

   <div class="card-body">

    <div class="panel panel-default">

      <div class="panel-body nunito-font">


        <form class="form" method="POST" action="{{ route('sms.store') }}"
        enctype='multipart/form-data'>
        @csrf
          <div class="form-group">
            <span class="text-muted">Sender</span>
            <div class="input-group">
              <span class="input-group-addon">sender</span>
              <input type="text"  class="form-control"  value="{{ config('app.name') }}" 
              name="senderContact" placeholder="Sender sending message..." 
              autocomplete="on" spellcheck="false">
            </div>
            @error('senderContact')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>

        
       

       <div class="form-group">
            <span class="text-muted">Receiver Telephone No.</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-phone" ></i></span>
              <input type="text"  class="form-control"  value="" 
              name="to" placeholder="Type phone number  *e.g +256700477421" 
              autocomplete="on" spellcheck="false" required>
            </div>
            @error('receiverName')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>

      <div class="form-group">
        <span class="text-muted">Message</span>
        <textarea class="form-control sms-message"  id="sms-message"
         name="message" spellcheck="true" required placeholder="Write your sms message here..."></textarea>
        @error('message')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div >

      <div class="col-lg-3">
        <div class="form-group">
          <input type="submit" class="btn btn-success nunito-font" value="Send SMS">
        </div>
      </div>

      <div class="row">

        <div class="col-lg-10">
       <div class="form-group text-center">
          @if(session()->get('success'))
          <div class='alert alert-success alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
             <span aria-hidden='true'>&times;</span></button>
             <strong>Yello!</strong> <span>{{ session()->get('success') }}</span>
           </div>
           @endif

           @if(session()->get('fail'))
           <div class='alert alert-danger alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
             <span aria-hidden='true'>&times;</span></button>
             <strong>Opps!</strong> <span>{{ session()->get('fail') }}</span>
           </div>
           @endif
         </div>
       </div>
     </div>
   </form>


 </div>
</div>
</div>
</div>
{{-- </div> --}}

<script>
 $(document).ready(function(){
  });
</script>
@endsection
