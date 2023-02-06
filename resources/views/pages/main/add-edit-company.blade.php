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

        <form class="form" method="post" action="{{ route('companies.register', isset($company)?$company['id']:0) }}"
          enctype='multipart/form-data'>
          @csrf

          <div class="form-group">
            <span class="text-muted"><span class="text-danger pr-2">*</span>Company Name</span>
            <input type="text"  class="form-control" placeholder="Enter company name" required
            name="company_name" value="<?= isset($company)? $company['company_name'] : ''?>" autocomplete="off">
          </div>

          <div class="form-group">
           <span class="text-muted"> Abbreviation</span>
           <input type="text" class="form-control"  placeholder="Enter company abbreviation" 
            name="company_abbrev" value="<?= isset($company)? $company['company_abbrev'] : ''?>"
           autocomplete="off">
         </div>

         <div class="form-group">
          <span class="text-muted"><span class="text-danger pr-2">*</span>Company Email</span>
          <input type="text" class="form-control" name="company_email"  placeholder="Enter company email" 
          value="<?= isset($company)? $company['company_email'] : ''?>" required
          autocomplete="off"
          >
        </div>

        <div class="form-group">
          <span class="text-muted"><span class="text-danger pr-2">*</span>Company Address</span>
          <input type="text" class="form-control"  placeholder="Enter company address" required
          name="company_address" value="<?= isset($company)? $company['company_address'] : ''?>"
          autocomplete="off">
        </div>

        <div class="form-group">
            <span class="text-muted">Company Moto</span>
            <textarea class="form-control" name="company_motto"  placeholder="Enter company motto" 
             ><?= isset($company)? $company['company_motto'] : ''?></textarea>
          </div>

        <div class="row form-group">
            <div class="col-lg-5">
          <span class="text-muted">Company Logo</span>
          <input type="file" class="form-control-file" name="company_logo" >
            </div>
            <div class="col-lg-3">
                <input type="submit" class="btn btn-sm overview-item--c4 text-white" 
                value="<?= isset($company)? 'Update' : 'Add Company'?>">
              </div>
        </div>

        <div class="row form-group">
         <div class="col-lg-9">
            <span class="pl-0 response"></span>
          </div>
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

