@extends('layouts.master')

@section('content')


{{-- <div class="card-table"> --}}
  <div class="card card-dashboard-table-six">

  <div class="card-header nunito-font">
     <i class="fa fa-dollar-sign text-success"></i> 
       {{ __('Payments / Mobile Money') }}
     </div>

  <div class="card-body">

    <div class="panel panel-default">
      <div class="panel-body nunito-font">

        <form class="form" method="post" action="{{ route('payments.request') }}">
         @csrf

         <div class="form-group">
          <span class="text-muted">Receiver</span>
          <span class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i></span>
            <input type="text"  class="form-control "
            name="Name" value="{{ config('app.name') }}" autocomplete="off" readonly>
          </span>
        </div>

        <div class="form-group">
         <span class="text-muted">{{ config('app.name') }} Mobile number</span>
         <span class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-phone"></i>
          </span>
          <input type="text" class="form-control " name="Contact" value="+256772833275"
          autocomplete="off" readonly>    
        </span>
      </div>

      <div class="form-group">
       <span class="text-muted">Customer Mobile No</span>
       <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
        <input type="text" class="form-control @error('phoneNo') is-invalid @enderror" name="phoneNo" value="{{ old('phoneNo') }}" placeholder="Enter customer mobile no to withdraw money from" 
        autocomplete="off">
        @error('phoneNo')
             <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
             @enderror
      </div>
    </div>

    <div class="form-group">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-comments text-dark"></i></span>
        <input type="text" class="form-control" name="reason"
        placeholder ="Enter reason"
        autocomplete="off">
      </div>
    </div>

   <div class="row">
    <div class="form-group col-lg-6">
      <span class="text-muted">Amount</span>
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-dollar-sign"></i></span>
        <input type="number" class="form-control @error('amount') is-invalid @enderror amount" name="amount" value="{{ old('amount') }}" 
        placeholder ="Enter amount of money"
        autocomplete="off">
          @error('amount')
             <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
             @enderror
      </div>
    </div>

    <div class="form-group col-lg-6">
        <span class="text-muted">MoMo Pin</span>
      <div class="input-group">
       <span class="input-group-addon"><i class="fa fa-lock"></i></span>
       <input type="password" id="MomoPin" name="MomoPin" class="form-control"
       placeholder = "Enter your Mobile Money pin">
       <span class="input-group-addon" id="showMomoPin">
        <i class="fa fa-eye"></i>
      </span>
    </div>
    </div>
  </div>

 <div class="row">
    <div class="col-lg-2 nunito-font">
       <button type="submit" class="btn btn-success">
      <i class="fa fa-lock pr-1" ></i> Pay shs.<span class="pl-1 btn-amount">0.00
      </span>
    </button>
   </div>

 
    <div class="col-lg-8 text-center">
     @if(session()->get('success'))
     <div class='alert alert-success alert-dismissible' role='alert'>
       <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <span aria-hidden='true'>&times;</span></button>
        <strong>Yello!</strong> {{ session()->get('success') }}<i class="fa fa-check-circle"></i>
      </div>
      @endif

      @if(session()->get('failed'))
      <div class='alert alert-danger alert-dismissible' role='alert'>
       <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <span aria-hidden='true'>&times;</span></button>
        <strong>Opps!</strong> {{ session()->get('failed') }}
      </div>
      @endif
       </div>

 </div>

   
  </form>




  <script>

  var momoPin = document.getElementById("MomoPin");

  $('#showMomoPin').click(function(){
    if (momoPin.type === "password") {
     momoPin.type = "text";
   }
   else {
     momoPin.type = "password";
   }
 });
      $('.amount').bind('input', function( event )
    {
     setAmt();
   });

    function setAmt()
    {
     var amount, amt;
     var amount = $('.amount').val();
     var amt = parseInt(amount).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 0});
     $('.btn-amount').text(amt);

   }


</script>


</div>
</div>
</div>
</div>
{{-- </div> --}}




@endsection


