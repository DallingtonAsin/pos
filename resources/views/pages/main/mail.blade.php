@extends('layouts.master')


@section('content')
{{-- <div class="card-table"> --}}
    <div class="panel panel-default nunito-font">
        <div class="panel-heading">
            <strong class="panel-title f-13">
                <i class="fa fa-envelope pr-2"></i> 
                Compose and send email
            </strong>
        </div>
      <div class="panel-body">
        <form class="form" method="post" action="{{ route('mail.store') }}"
        enctype='multipart/form-data'>
        @csrf


          <div class="row">

            <div class="col-md-3 mt-4">
                <span class="text-muted">To all cashiers?</span>
                <input type="radio" id="Cyes" class='radioBtn yesBtn'  name="toAllCashiers" value="yes">
                <label for="yes">Yes</label>
                <input type="radio" id='Cno' class='radioBtn noBtn'  name="toAllCashiers" value="no" checked>
                <label for="no">No</label>
             </div>   

             <div class="col-md-3 mt-4">
                <span class="text-muted">To all managers?</span>
                <input type="radio" id="Myes" class='radioBtn yesBtn'  name="toAllManagers" value="yes">
                <label for="Myes">Yes</label>
                <input type="radio" id='Mno' class='radioBtn noBtn'  name="toAllManagers" value="no" checked>
                <label for="Mno">No</label>
             </div>  
        </div>
      
          <div class="form-group">
            <span class="text-muted">Sender</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user" ></i></span>
              <input type="text"  class="form-control   @error('senderName') is-invalid @enderror"  value="{{ Auth::user()->name}}" 
              name="senderName" placeholder="type your name" value="{{ Auth::user()->name }}"
              autocomplete="off" id='sender' spellcheck="false" disabled>
            </div>
            @error('senderName')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>

       <div class="form-group RecipientNameDiv">
            <span class="text-muted">Recipient</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user" ></i></span>
              <input type="text"  class="form-control " 
              name="receiverName" placeholder="type recipient's name" id='recipient' value="{{ old('receiverName') }}"
              autocomplete="off" spellcheck="false">
            </div>
            @error('receiverName')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>


        <div class="form-group">
        <span class="text-muted">Subject</span>
        <div class="input-group">
          <span class="input-group-addon"><i class="fas fa-heading" ></i></span>
          <input type="text" class="form-control  " id="subject" name="subject"
           value="{{  old('subject') }}" placeholder="type your email subject or heading" 
          autocomplete="off">
        </div>
        @error('subject')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div >

         {{-- <div class="form-group">
          <span class="text-muted">Sender's email</span>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope" ></i></span>
            <input type="text"  class="form-control "  placeholder="type your email" 
            name="senderEmail" value="{{ Auth::user()->email}}" disabled
            autocomplete="off" spellcheck="false">
          </div>
          @error('senderEmail')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div> --}}

      <div class="form-group RecipientEmailDiv">
         <span class="text-muted">Recipient's email</span>
         <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-envelope" ></i></span>
          <input type="email" class="form-control "
           placeholder="type recepient's email" 
           name="receiverEmail" value="{{ old('receiverEmail') }}" id='receiverEmail'
          autocomplete="on"  >
        </div>
        @error('receiverEmail')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

    <div class="row form-group">

      <div class="col-md-8">
        <span class="text-muted">Message</span>
        <textarea class="form-control "  id="email-message" value="{{ old('message') }}"
         name="message" spellcheck="true" placeholder="Write your email message here"></textarea>
        @error('message')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

      <div class="col-md-4 pt-4">
            <span class="text-muted">Attachment</span>
            <input type="file" id='attachment' class="form-control-file " name="email-attachment[]" multiple >
          </div>
        </div>


        <div class="row">
         <div class="col-lg-3">
           <input type="submit" id='sendMailBtn' class="btn overview-item--c2 text-white nunito-font" value="Send Email">
         </div>
         <div class="col-lg-9">
            <span class="pl-0 response"></span>
          </div>
         </div>


  
          {{-- @if(session()->get('success'))
          <div class="form-group text-center">
          <div class='alert alert-success alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
             <span aria-hidden='true'>&times;</span></button>
             <strong>Yello!</strong> <span>{{ session()->get('success') }}</span>
           </div>
         </div>
           @endif

           @if(session()->get('fail'))
           <div class="form-group text-center">
           <div class='alert alert-danger alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
             <span aria-hidden='true'>&times;</span></button>
             <strong>Opps!</strong> <span>{{ session()->get('fail') }}</span>
           </div>
         </div> 
           @endif
           --}}

  </form>
</div>
</div>
{{-- </div> --}}
<script src="{{ asset('vendors/notify/notify.js') }}"></script>
@if(session()->get('success'))
<script>
    $(document).ready(function(){
     var LoginMessageError = "{{ session()->get('success') }}";
      ShowLoginErrorMessage(LoginMessageError);
      function ShowLoginErrorMessage(messageString)
    {
      $(".response").notify(messageString,{
        className: 'success',
        autoHide: true,
        clickToHide:true,
        autoHideDelay:45000,
       });
    }
    });
</script>
@endif  

@if(session()->get('error'))
<script>
    $(document).ready(function(){
     var LoginMessageError = "{{ session()->get('error') }}";
      ShowLoginErrorMessage(LoginMessageError);
      function ShowLoginErrorMessage(messageString)
    {
      $(".response").notify(messageString,{
        className: 'error',
        autoHide: true,
        clickToHide:true,
        autoHideDelay:45000,
       });
    }
    });

</script>
@endif
 



<script>
 $(document).ready(function(){

    var toAllCashiers, toAllManagers, loggedInUserEmail;
   
    loggedInUserEmail = "{{ Auth::user()->email }}";
    if(loggedInUserEmail)
    {
        
        $('input[name=toAllCashiers]').change(function(e) {
         var data = $(this).val();
         var data2 = $("input[name='toAllManagers']:checked").val();
         HideShowEmailField(data, data2);
        });

        $('input[name=toAllManagers]').change(function(e) {
         var data = $(this).val();
         var data2 = $("input[name='toAllCashiers']:checked").val();
         HideShowEmailField(data, data2);
        });


        
    }else
    {
        $("#sender").prop('disabled', true);
        $("#recipient").prop('disabled', true);
        $("#receiverEmail").prop('disabled', true);
        $("#subject").prop('disabled', true);
        $("#email-message").prop('disabled', true);
        $("#attachment").prop('disabled', true);
        $("#sendMailBtn").prop('disabled', true);
        $(".radioBtn").prop('disabled', true);
    }

    function HideShowEmailField(resp1, resp2)
    {
       var receiver =  $("#recipient").val();
       var receiverEmail = $("#receiverEmail").val();
        switch(true){
            case (resp1 == "yes" && resp2 == "yes"):
            $(".RecipientNameDiv").hide(); 
            $(".RecipientEmailDiv").hide();
            break;
            case (resp1 == "yes" && resp2 == "no"):
            $(".RecipientNameDiv").hide();    
            $(".RecipientEmailDiv").hide();
            break;
            case (resp1 == "no" && resp2 == "yes"):
            $(".RecipientNameDiv").hide();     
            $(".RecipientEmailDiv").hide();
            break;
            case (resp1 == "no" && resp2 == "no"):
            $(".RecipientNameDiv").show();     
            $(".RecipientEmailDiv").show();
            break;
        }
   
    }

  });
</script>


@endsection
