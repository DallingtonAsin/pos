@extends('layouts.master')

@section('content')

   <div class="card card-dashboard-table-six">
  <div class="card-title">
       <strong class="col-lg-10 text-success">
        <i class="fa fa-gear"></i>
         <span class="text-dark">
          Company / <?= isset($company)? 'Edit details' : 'Add company'?>
        </span>
      </strong>
</div>

  <div class="card-body pt-3">
    <div class="panel panel-default">

      <div class="panel-body">

        <form class="form" method="post" action="{{ route('companies.register', $company->is_registered ? $company->id : 0) }}"
          enctype='multipart/form-data'>
          @csrf

          <div class="form-group">
            <span class="text-muted"><span class="text-danger pr-2">*</span>Name</span>
            <input type="text"  class="form-control" placeholder="Enter company name" 
            name="name" value="<?= isset($company)? $company->name : ''?>" autocomplete="off">
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <span class="text-muted"><span class="text-danger pr-2">*</span>Phone Number</span>
            <input type="text" class="form-control" name="phone_number"  
            placeholder="Enter phone number" value="<?= isset($company)? $company->phone_number : ''?>"
             >
             @error('phone_number')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>


         <div class="form-group">
          <span class="text-muted"><span class="text-danger pr-2">*</span>Email</span>
          <input type="text" class="form-control" name="email"  placeholder="Enter email" 
          value="<?= isset($company)? $company->email : ''?>" 
          autocomplete="off"
          >
          @error('email')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <span class="text-muted"><span class="text-danger pr-2">*</span>Address</span>
          <input type="text" class="form-control"  placeholder="Enter address" 
          name="address" value="<?= isset($company)? $company->address : ''?>"
          autocomplete="off">
          @error('address')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

      
          <div class="form-group">
            <span class="text-muted"> Abbreviation</span>
            <input type="text" class="form-control"  placeholder="Enter abbreviation" 
             name="abbrev" value="<?= isset($company)? $company->abbrev : ''?>" autocomplete="off">
          </div>

        <div class="form-group">
          <span class="text-muted">Company Logo</span>
          <input type="file" class="form-control-file" name="logo">
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success text-white" 
            value="<?= isset($company)? 'Update' : 'Submit'?>">
        </div>

      

       </form>
     </div>
   </div>
 </div>
</div>


<script src="{{ asset('vendors/notify/notify.js') }}"></script>

@if(session()->get('success'))
<script>
    $(document).ready(function(){
     var div = ".response";
     var type = "success";
     var LoginMessageError = "{{ session()->get('success') }}";
     ShowLoginErrorMessage(div, type, LoginMessageError);
    });

</script>
@endif  

@endsection

