@extends('layouts.master')

@section('content')


  <div class="card card-dashboard-table-six">
    <div class="card-body">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="panel-tile">
            <div class="row nunito-font">
              <div class="col-lg-2">
                <h6 class="text-dark">
                  <i class="fa fa-home text-success"> /</i>
                  <strong>Company details</strong>
                  <span class="badge nunito-font  totl_customers">
                      @isset($number_of_companies)
                      {{ number_format($number_of_companies) }}
                      @endisset
                    </span>
                </h6>
              </div>
              <div class="col-lg-2">
                <h5>
                    <a class="text-info bolded" href="javascript:void(0)"
                     id="createNewCompany"> Add company</a>
                </h5>
              </div>
              <div class="col-lg-2">
               <div class="btn-group">
                <button type="button" class="btn border-info text-success bolded form-control text-center dropdown-toggle downloadfilebtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Action
               </button>
               <ul class="dropdown-menu">
                 @can('isAdmin')
                 <li>
                <a class="text-decoration-none text-dark
                nunito-font"
                href="javascript:void(0)"
                id="removeAllCustomers"> Delete all company</a>
                 </li>
                @endcan
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="panel-body">
        <div class="col-lg-8 text-center nunito-font">
            @if(session()->get('success'))
            <div class='alert alert-success alert-dismissible' role='alert'>
             <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span></button>
              <strong>Yello!</strong> {{ session()->get('success') }}<i class="fa fa-check-circle"></i>
            </div>
            @endif

            @if(session()->get('fail'))
            <div class='alert alert-danger alert-dismissible' role='alert'>
             <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span></button>
              <strong>Oops!</strong> {{ session()->get('fail') }}
            </div>
            @endif

          </div>

      <div class="table table-sm table-responsive custom-family" >
        <table class="table table-bordered table-hover companies-table" id="companies-table">
            <thead>
              <tr>
                <th class="td-sm">No</th>
                <th>Name</th>
                <th>Abbreviation</th>
                <th>Email</th>
                <th>Address</th>
                <th>Motto</th>
                <th>Action</th>
              </tr>
            </thead>
        </table>
</div>
</div>
</div>
</div>
</div>


<!--Add company -->
<div class="modal fade nunito-font addCompanyModal" id="addCompanyModal" tabindex="-1"
role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form name="company" id="CompanyForm">
          @csrf
       <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new company</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="form-group">
            {{-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> --}}
            <input type="hidden" class="form-control companyId bg-white companyId" name="id"
             placeholder="Enter customer id"  Required autofocus>
          </div>

        <div class="form-group">
          <span>Name</span>
          <input type="text" class="form-control company_name bg-white" 
          name="company_name" placeholder="Enter company name" Required autofocus>
        </div>

       
        <div class="form-group">
          <span>Abbreviation</span>
          <input type="text" class="form-control company_abbrev bg-white"
           name="company_abbrev" placeholder="Enter company abbreviation" autofocus>
        </div>

        <div class="form-group">
          <span>Email</span>
          <input type="email" class="form-control company_email bg-white"
           name="company_email" placeholder="Enter company email">
        </div>


        <div class="form-group">
          <span>Address</span>
          <input type="text" class="form-control company_address bg-white" name="company_address"
           placeholder="Enter company address">
        </div>

        <div class="form-group">
            <span>Motto</span>
            <textarea class="form-control company_motto bg-white" placeholder="Enter company motto"></textarea>
          </div>

        <div class="form-group">
          <button type="submit" class="btn btn-primary AddcompanyBtn"  name="AddcompanyBtn">Save</button>
          <button type="reset" class="btn btn-danger clearBtn">Clear</button>
          <button type="button" class="btn btn-dark closeBtn" data-dismiss="modal">Close</button>
        </div>

        <div class="form-group">
          <span class="errors-section text-danger nunito-font"></span>
        </div>

      </div>
    </form>
  </div>
</div>
</div>


 <!--Modal Deletecompany -->
 <div class="modal fade" id="deleteCompanyModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title delete-modal-title w-100 font-weight-bold">Delete company</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <div class="text-center">
             <label class="text-danger delete-alert-text">
             Are you sure you want to delete this company
               <small class="text-dark text-muted bolded">
               </small>
               ?

             </label>
           </div>
         </div>

         <div class="form-group">
            <button type="submit" class="btn btn-primary delete-ok-btn"  name="ConfirmBtn">Yes</button>
            <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end of modal Deletescustomers-->


{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script> --}}
<script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('vendors/notify/notify.js') }}"></script>


<script type="text/javascript">
  $(document).ready(function(){
     $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });

      // $.fn.dataTable.ext.errMode = 'none';
      // $('#companies-table').on( 'error.dt', function ( e, settings, techNote, message ) {
      // console.log( 'An error has been reported by DataTables: ', message );
      // } ) .DataTable();


        $('#createNewCompany').click(function (e) {
         e.preventDefault();
         DisableTableFields(false);
         ShowBtns();
        $('.AddcompanyBtn').text("Register company");
        $('.companyId').val('');
        $('#CompanyForm').trigger("reset");
        $('#modalHeading').html("Register new company");
        $('#addCompanyModal').modal('show');
    });


    //code that displays results of the table index()
    var table = $('#companies-table').dataTable({
     dom: 'Bfrtip',
     paging:true,
     select:true,
     "autoWidth": false,
     "bLengthChange": true,
     oLanguage: {
     sLengthMenu: "Show _MENU_ entries",
     },
     processing:true,
     serverSide:true,
     stateSave: true,
     deferRender: true,
     ajax:"{{ route('companies.home') }}",
     columns: [
         {data: 'id', name:'id'},
         {data: 'company_name', name:'company_name'},
         {data: 'company_abbrev', name:'company_abbrev'},
         {data: 'company_email', name:'company_email'},
         {data: 'company_address', name:'company_address'},
         {data: 'company_motto', name:'company_motto'},
         {data: 'action', name: 'action',orderable: false,searchable: false},
     ],
     order: [[0, 'asc']]
    });


//modal used to edit customer details [each row of the tbl]
    $('body').on('click', '#edit-company', function (event) {
      var customer_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('customers.index') }}" +'/' + customer_id +'/edit', function (data) {

          $('#modalHeading').html("Edit details of customer " + data.name + "");
          $('.AddcompanyBtn').text("Edit customer");
          $('#addCompanyModal').modal('show');
          $('.companyId').val(data.id);
          $('.name').val(data.name);
          $('.contact').val(data.contact);
          $('.debt').val(data.debt);
          $('.credit').val(data.credit);
          DisableTableFields(false);
          ShowBtns();
      })
   });


   //View Modal used to view each row [customer details]
   $('body').on('click', '#view-customer', function (event) {
      var customer_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('customers.index') }}" +'/' + customer_id +'', function (data) {

          $('#modalHeading').html("Details of customer " + data.name + "");
          $('#addCompanyModal').modal('show');
          $('.companyId').val(data.id);
          $('.name').val(data.name);
          $('.contact').val(data.contact);
          $('.debt').val(data.debt);
          $('.credit').val(data.credit);
          DisableTableFields(true);
          HideBtns();
      })
   });


    $('.AddcompanyBtn').click(function (e) {

        e.preventDefault();

        var Errors = validateForm();
        if(Errors.length == 0){
        $(this).html('Sending..');

        $.ajax({
          data: $('#CompanyForm').serialize(),
          url: "{{ route('customers.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#CompanyForm').trigger("reset");
              $('#addCompanyModal').modal("hide");
              var resp = data.success;
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('#companies-table').DataTable();
              tbl.ajax.reload();

          },
          error: function (data) {
              console.log('Error:', data.error);
              ShowResponse('.response', data.error, 'error');
              $('.AddcompanyBtn').html('Save Changes');
          }
      });
        }else
        {
            var i;
            var message ="";
            for(i=0; i<Errors.length; i++){
                message += Errors[i] + "<br>";
            }
            $('.errors-section').html(message);

        }

    });

   //this pops up confirm delete modal
    $('body').on('click', '#delete-customer', function (e) {
            var customer_id = $(this).data("id");
            e.preventDefault();
            $("#deleteCompanyModal").modal('show');
            $(".delete-alert-text").html("Are you sure you want to delete this customer?");
            $('.delete-ok-btn').on('click', function(){
                   ListenAndDoDeletion(customer_id);
         });

 });


 function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("customers.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
     $('.delete-ok-btn').html('Deleting...');
        $.ajax({
         type: "DELETE",
         url: deleteUrl,
         success: function (data) {
              var resp = data.success;
              $('.delete-ok-btn').html('Yes');
              $('#deleteCompanyModal').modal("hide");
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('#companies-table').DataTable();
              tbl.ajax.reload();
         },
         error: function (data) {
             console.log('Error:', data);
             ShowResponse('.response', data.error, 'error');
         }
     });
 }


  function DisableTableFields(bool){

          $('.companyId').attr('disabled', bool);
          $('.name').attr('disabled', bool);
          $('.contact').attr('disabled', bool);
          $('.debt').attr('disabled', bool);
          $('.credit').attr('disabled', bool);
  }

  function HideBtns(){
          $('.AddcompanyBtn').hide();
          $('.clearBtn').hide();
          $('.closeBtn').hide();
  }

  function ShowBtns(){
          $('.AddcompanyBtn').show();
          $('.clearBtn').show();
          $('.closeBtn').show();
  }

  function ShowResponse(area, message, errorType)
    {
      $(area).notify(message,{
        className: errorType,
        autoHide: true,
        clickToHide:true,
        autoHideDelay:45000,
       });
    }

  function FormatNumber(number){
   var FormattedNumber = parseFloat(number).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 0});
   return FormattedNumber;
  }

function ResetTblInfo(response)
 {
     var totl_number , sum_of_credits, sum_of_debts;
     totl_number = FormatNumber(response.totl_no);
     sum_of_credits = FormatNumber(response.totl_credit);
     sum_of_debts = FormatNumber(response.totl_debt);

     $('.totl_customers').html(totl_number);
     $('.totl_credit').html(sum_of_credits);
     $('.totl_debt').html(sum_of_debts);
 }

 function validateForm()
 {
    var name = $('.name').val();
    var address = $('.address').val();
    var contact = $('.contact').val();
    var errors = [];
    if(name.length < 1){
      var nameErr = "Please enter the name of the customer";
      errors.push(nameErr);
    }
    if(contact.length < 1){
     var contactErr = "Please enter customer's contact";
     errors.push(contactErr);
    }

      return errors;

 }



 $("#removeAllCustomers").bind("click", function(){
   removeAllCustomers();
 });

 function removeAllCustomers(){
 $.confirm({
   boxWidth: '30%',
   icon: 'fa fa-warning',
   theme:'light',
   closeIcon: true,
   draggable:true,
   closeIconClass: 'fa fa-close text-danger',
   title: 'Delete all customers',
   content:'Are you sure you want to remove all customers',
   buttons:{
       confirm:function(){
     var self = this;
     return $.ajax({
         data: {
             "_token": "{{ csrf_token() }}",
             },
         url: '{{ Route("customers.truncate") }}',
         type: 'POST',
         // dataType: 'json',
     }).done(function (data) {

         $.alert({
             title: 'Message',
             content: data.success,
         });
          $(".totl_customers").text(data.totl_no);
          $(".totl_credit").text(data.totl_credit);
          $(".totl_debt").text(data.totl_debt);
          var tbl = $('#companies-table').DataTable();
          tbl.ajax.reload();


     }).fail(function(data){
         $.alert({
             title: 'Response',
             content:"Customers not deleted:"+data.fail,
         });
         console.log(data);

     });

       },
       cancel:function(){

       }
   },
 });

   }




  });

</script>

@endsection
