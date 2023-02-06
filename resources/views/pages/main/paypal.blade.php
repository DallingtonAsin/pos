@extends('layouts.master')

@section('content')


{{-- <div class="card-table"> --}}
  <div class="card card-dashboard-table-six">

    <div class="card-header nunito-font w3-text-blue">
     {{ __('Paypal Card Payment') }}
   </div>


   <div class="card-body">

    <div class="panel panel-default">
      <div class="panel-body nunito-font">

       <form method="POST" action="{{ route('paypal-payment-form-submit') }}">
         @csrf
         <div class="form-group row">
           <div class="col-md-12">
             <input id="card_no" type="text" class="form-control @error('card_no') is-invalid @enderror" name="card_no" value="{{ old('card_no') }}" autocomplete="card_no" placeholder="Card No." autofocus>
             @error('card_no')
             <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
             @enderror
           </div>
         </div>
         <div class="form-group">
           <input id="exp_month" type="text" class="form-control @error('exp_month') is-invalid @enderror" name="exp_month" value="{{ old('exp_month') }}" autocomplete="exp_month" placeholder="Expiry Month (Eg. 02)" autofocus>
           @error('exp_month')
           <span class="invalid-feedback" role="alert">
             <strong>{{ $message }}</strong>
           </span>
           @enderror
         </div>

         <div class="form-group">
           <input id="exp_year" type="text" class="form-control @error('exp_year') is-invalid @enderror" name="exp_year" value="{{ old('exp_year') }}" autocomplete="exp_year" placeholder="Expiry Year (Eg. 2020)" autofocus>
           @error('exp_year')
           <span class="invalid-feedback" role="alert">
             <strong>{{ $message }}</strong>
           </span>
           @enderror
         </div>

         <div class="form-group row">
           <div class="col-md-12">
             <input id="cvv" type="password" class="form-control @error('cvv') is-invalid @enderror" name="cvv" autocomplete="current-password" placeholder="CVV">
             @error('cvv')
             <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
             @enderror
           </div>
         </div>


         <div class="form-group row">
           <div class="col-md-12">
             <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" autocomplete="current-password" placeholder="Amount">
             @error('amount')
             <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
             @enderror
           </div>
         </div>


         <div class="form-group row mb-0">

           <div class="col-md-2">
             <button type="submit" class="btn btn-success br-30 btn-block nunito-font">
               {{ __('PAY NOW') }}
             </button>
           </div>

           <div class="col-md-8 text-center">
            @if(session()->get('success'))
            <div class='alert alert-success alert-dismissible' role='alert'>
             <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span></button>
              <strong>Yello!</strong> {{ session()->get('success') }}<i class="fa fa-check-circle"></i>
            </div>
            @endif

            @if(session()->get('error'))
            <div class='alert alert-danger alert-dismissible' role='alert'>
             <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span></button>
              <strong>Opps!</strong> {{ session()->get('error') }}
            </div>
            @endif
          </div>



        </div>

      </form>


    </div>
  </div>
</div>
</div>
{{-- </div> --}}

@endsection
