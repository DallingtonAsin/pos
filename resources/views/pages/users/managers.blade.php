@extends('layouts.master')

@section('content')

      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="panel-tile">

            <div class="row nunito-font">

              <span class="response"></span>
              <div class="col-lg-6">
                <h5 class="text-dark">
                  <i class="fa fa-home text-success"> /</i>
                  <strong>Registered Managers</strong>
                  <span class="badge nunito-font  totl_managers">
                      @isset($number_of_managers)
                      {{ number_format($number_of_managers) }}
                      @endisset
                    </span>
                </h5>
              </div>

              {{-- <div class="col-lg-3">
               <h5 class="text-dark">
                  <a href="javascript:void(0)" id="createNewmanager"
                                class="add-link text-decoration-none bolded">
                                    Add manager</a></h5>
              </div> --}}

          </div>

        </div>
      </div>

      <div class="panel-body">

    
<div class="row">
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
           </div>

      <div class="table-responsive custom-family" >

        <table class="table table-bordered table-hover managers-table" id="managers-table">

            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Username</th>
                <th>Gender</th>
                <th>Contact</th>
                <th>A/C status</th>
                <th>A/C Action</th>
                <th>Action</th>
              </tr>
            </thead>
        </table>
</div>
</div>
</div>


<!--Add manager -->
<div class="modal fade nunito-font" id="addmanagersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <form mname="user" id="userForm">
       @csrf

       <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold modalHeading" id="modalHeading">Add new manager</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="form-group">
         <input type="hidden" class="userId" name="id">
          <span><span class="text-danger">*</span> First Name</span>
          <input type="text" class="form-control first_name " name="firstName" placeholder="Enter first name" Required autofocus>
        </div>

          <div class="form-group">
            <span><span class="text-danger">*</span> Last Name</span>
            <input type="text" class="form-control last_name " name="lastName" placeholder="Enter last name" Required autofocus>
          </div>
      

        <div class="form-group">
          <span><span class="text-danger">*</span> Address</span>
          <input type="text" class="form-control address " name="address" placeholder="Enter address" Required autofocus>
        </div>

        <div class="form-group">
            <span>NationalID No.</span>
            <input type="text" class="form-control national_id " name="NationalIDNo" placeholder="Enter NationalID number(optional)" Required autofocus>
          </div>

        <div class="form-group">
          <span> Email</span>
          <input type="email" class="form-control email " name="email" placeholder="Email (optional)">
        </div>

        <div class="row form-group">
            <div class="col-md-6">
              <span><span class="text-danger">*</span> Primary Tel No.</span>
              <input type="text" class="form-control tel_no " name="tel_no" placeholder="Enter primary telephone number" Required autofocus>
            </div>
    
            <div class="col-md-6">
                <span>Alternative Tel No.</span>
                <input type="text" class="form-control alt_telno " name="alt_telno" placeholder="Enter alternative telephone number (optional)" >
              </div>
    </div>


    <div class="row form-group">
        <div class="col-md-6">
          <div class="form-group">
              <span><span class="text-danger">*</span> Role</span>
              <select class="form-control role_section " name="role">
              <option value="">select role</option>
              </select>
            </div>
        </div>
          <div class="col-md-6">
            <span><span class="text-danger">*</span> Gender</span>
            <select class="form-control gender " name="gender">
                <option value="">select gender</option>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
            </select>
          </div>
            <span class="errors-section text-danger"></span>
    </div>

        <div class="form-group">
          <button type="button" class="btn btn-success AdduserBtn" id="AdduserBtn"  name="AdduserBtn">Save</button>
          <button type="reset" class="btn btn-danger">Clear</button>
          <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>
</div>


<!--Import managers -->
<div class="modal fade nunito-font" id="importmanagers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <form action="" method="post"
      enctype="multipart/form-data" name="inportExpensesForm" >
      @csrf

      <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold">
        Import an excel file of managers </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="form-group">
          <span>Select file for Upload</span>
        </div>

        <div class="form-group">
          <input type="file" class="form-control-file @error('select_file') is-invalid @enderror" name="select_file" Required autofocus>
        </div>

        @error('select_file')
        <div class='alert alert-danger alert-dismissible text-center' role='alert'>
          <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Sorry!</strong> {{ $message }}
          </div>
          @enderror

          <div class="form-group">
            <button type="submit" class="btn btn-success">Upload</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


 <!--Modal Deletemanagers -->
 <div class="modal fade" id="deletemanagersModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title delete-modal-title w-100 font-weight-bold">Delete manager</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <div class="text-center">
             <label class="text-danger delete-alert-text">Are you sure you want to delete this manager
               <small class="text-dark text-muted bolded">
               </small>
               ?

             </label>
           </div>
         </div>

         <div class="form-group">
            <button type="submit" class="btn btn-success delete-ok-btn"  name="ConfirmBtn">Yes</button>
            <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end of modal Deletemanagers-->


 <div class="modal fade" id="accountChangeModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title change-account-modal-title w-100 font-weight-bold">Lock or unlock user account</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <div class="text-center">
             <label class="text-danger account-change-alert-text">Are you sure you want lock or unlock this account?
               <small class="text-dark text-muted bolded">
               </small>
               ?

             </label>
           </div>
         </div>

         <div class="form-group">
            <button type="submit" class="btn btn-success confirm-changeAccount-ok-btn"  name="ConfirmChangeBtn">Yes</button>
            <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>
</div> 

<script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('vendors/notify/notify.js') }}"></script>
<script>
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });
  const ajaxUrl = @json(route('managers.index.ajax'));
  const deletedSeletectedUrl = @json(route('selected-users.remove'));
  const cat = 'manager';
  const token = "{{ csrf_token() }}";
</script>

<script type="text/javascript">
  $(document).ready(function(){
   

     //code that displays results of the table index()
    var table = $('#managers-table');
    var title = "List of registered managers in the system";
    var columns = [0, 1];
    var dataColumns = [
         {data: 'checkbox', name:'checkbox'},
        //  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
         {data: 'name', name:'name'},
         {data: 'username', name:'username'},
         {data: 'gender', name:'gender'},
         // {data: 'address', name:'address'},
         {data: 'tel_no', name:'tel_no'},
         {data: 'isActive', name:'isActive'},
         {data: 'accountAction', name:'accountAction'},
         {data: 'action', name: 'action',orderable: false,searchable: false},
     ];

    makeDataTable(table, title, columns, dataColumns);
      
   $('#createNewmanager').click(function (e) {
         e.preventDefault();
         DisableTableFields(false);
         ShowBtns();
        $('.AdduserBtn').text("Register manager");
        $('.managerId').val('');
        $('#userForm').trigger("reset");
        $('#modalHeading').html("Register new manager");
        $('#addmanagersModal').modal('show');
    });


Numberize(".debt");
Numberize(".credit");

PopulateRoles();
function PopulateRoles(){
  var fetchRolesByAjaxUrl = "{{ route('roles.ajax.fetch') }}"
    $.ajax({
            type: "GET",
            url: fetchRolesByAjaxUrl,
            success: function(resp){
                var obj = JSON.parse(resp);
                for(var i = 0; i < obj.length; i++) {
                    let role_id = obj[i]['role_id'];
                    let role_name = obj[i]['role'];
                    $('.role_section').append('<option value=' + role_id + '>' + role_name + '</option>');
                }
            }
        });
  }


function Numberize(i){
  $(document).on("keyup", i , function(){
  if(this.value.length > 0){
    var n = parseInt(this.value.replace(/\D/g,''), 10);
    $(this).val(n.toLocaleString());
  }
});
}

//modal used to edit managers details [each row of the tbl]
    $('body').on('click', '#edit-user', function (event) {
      var manager_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('users.index') }}" +'/' + manager_id +'/edit', function (data) {

          $('#modalHeading').html("Edit details of manager " + data.name + "");
          $('.AdduserBtn').text("Edit manager");
          $('#addmanagersModal').modal('show');
          $('.userId').val(data.id);
          $('.first_name').val(data.first_name);
          $('.last_name').val(data.last_name);
          $('.address').val(data.address);
           $('.email').val(data.email);
          $('.national_id').val(data.nationalID_no);
          $('.tel_no').val(data.tel_no);
          $('.alt_telno').val(data.alt_telno);
          $('.role_section').val(data.user_role);
          $('.gender').val(data.gender);
          DisableTableFields(false);
          ShowBtns();
      })
   });


   //View Modal used to view each row [managers details]
   $('body').on('click', '#view-user', function (event) {
      var manager_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('users.index') }}" +'/' + manager_id +'', function (data) {

          $('#modalHeading').html("Details of manager " + data.name + "");
          $('#addmanagersModal').modal('show');
          $('.userId').val(data.id);
          $('.first_name').val(data.first_name);
          $('.last_name').val(data.last_name);
          $('.address').val(data.address);
          $('.email').val(data.email);
          $('.national_id').val(data.nationalID_no);
          $('.tel_no').val(data.tel_no);
          $('.alt_telno').val(data.alt_telno);
          $('.role_section').val(data.user_role);
          $('.gender').val(data.gender);
          DisableTableFields(true);
          HideBtns();
      })
   });


    $('.AdduserBtn').click(function (e) {

        e.preventDefault();
    
        var Errors = validateForm();
        if(Errors.length == 0){
        $(this).html('Sending..');

        $.ajax({
          data: $('#userForm').serialize(),
          url: "{{ route('users.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#userForm').trigger("reset");
              $('#addmanagersModal').modal("hide");
              var tbl = $('#managers-table').DataTable();
              tbl.ajax.reload();
              var resp = data.success;
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);

          },
          error: function (data) {
              console.log('Error:', data.error);
              ShowResponse('.response', data.error, 'error');
              $('.AdduserBtn').html('Save Changes');
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
    $('body').on('click', '#delete-user', function (e) {
            var manager_id = $(this).data("id");
            e.preventDefault();
            $("#deletemanagersModal").modal('show');
            $(".delete-alert-text").html("Are you sure you want to delete this manager?");
            $('.delete-ok-btn').on('click', function(){
                   ListenAndDoDeletion(manager_id);
         });

 });

     function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("users.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
     $('.delete-ok-btn').html('Deleting...');
        $.ajax({
         type: "DELETE",
         url: deleteUrl,
         success: function (data) {
              var resp = data.success;
              $('.delete-ok-btn').html('Yes');
              $('#deletemanagersModal').modal("hide");
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('#managers-table').DataTable();
              tbl.ajax.reload();
         },
         error: function (data) {
             console.log('Error:', data);
             ShowResponse('.response', data.error, 'error');
         }
     });
 }



    $('body').on('click', '#changeAccountBtn', function (e) {
           e.preventDefault();
            var user_id = $(this).data("id");
            var account_status = $(this).data("status");
            var name = $(this).data("name");
            var statusText;
            if(account_status){
              statusText = 'deactivate';
              $('.change-account-modal-title').html("Deactivate user account");
              $('.confirm-changeAccount-ok-btn').html('Deactivate');
              }else {
               statusText = 'activate';
              $('.change-account-modal-title').html("Activate user account");
              $('.confirm-changeAccount-ok-btn').html('Activate');
             }
            $("#accountChangeModal").modal('show');
            $(".account-change-alert-text").html("Are you sure you want to "
              +statusText+" "+name+"'s account?");
            $('.confirm-changeAccount-ok-btn').on('click', function(){
                   ChangeAccountStatus(user_id, account_status);
         });

 });



function ChangeAccountStatus(id, status){
    var accountChangeUrl = '{{ route("account.change") }}';
    var btnText;
    (status) ? btnText = 'Deactivating...' : btnText = 'Activating...';
    $('.confirm-changeAccount-ok-btn').html(btnText);
        $.ajax({
         type: "POST",
         url: accountChangeUrl,
         data: {id:id, status:status},
         success: function (data) {
              var resp = data.success;
              $('.confirm-changeAccount-ok-btn').html('Yes');
              $('#accountChangeModal').modal("hide");
              ShowResponse('.response', resp, 'success');
              var tbl = $('#managers-table').DataTable();
              tbl.ajax.reload();
         },
         error: function (data) {
             console.log('Error:', data);
             ShowResponse('.response', data.error, 'error');
         }
     });
    }


  function DisableTableFields(bool){

          $('.userId').attr('disabled', bool);
          $('.first_name').attr('disabled', bool);
          $('.last_name').attr('disabled', bool);
          $('.address').attr('disabled', bool);
          $('.email').attr('disabled', bool);
          $('.national_id').attr('disabled', bool);
          $('.tel_no').attr('disabled', bool);
          $('.alt_telno').attr('disabled', bool);
          $('.role_section').attr('disabled', bool);
          $('.gender').attr('disabled', bool);
  }

  function HideBtns(){
          $('.AdduserBtn').hide();
          $('.clearBtn').hide();
          $('.closeBtn').hide();
  }

  function ShowBtns(){
          $('.AdduserBtn').show();
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

     $('.totl_managers').html(totl_number);
     $('.totl_credit').html(sum_of_credits);
     $('.totl_debt').html(sum_of_debts);
 }

 function validateForm()
 {
    var first_name = $('.first_name').val();
    var last_name = $('.last_name').val();
    var address = $('.address').val();
    var national_id = $('.national_id').val();
    var tel_no = $('.tel_no').val();
    var role = $('.role_section').val();
    var gender = $('.gender').val();

    var errors = [];
    if(first_name.length < 1){
      var fnameErr = "Please enter the first name of the manager";
      errors.push(fnameErr);
    }
    if(last_name.length < 1){
      var lnameErr = "Please enter the last name of the manager";
      errors.push(lnameErr);
    }
    if(address.length < 1){
      var addressErr = "Please enter the address of the manager";
      errors.push(addressErr);
    }
  
    if(tel_no.length < 1){
      var contactErr = "Please enter the primary telephone number of the manager";
      errors.push(contactErr);
    }
    if(role.length < 1){
     var roleErr = "Please enter user's role";
     errors.push(roleErr);
    }

    if(gender.length < 1){
     var genderErr = "Please enter user's gender";
     errors.push(genderErr);
    }

      return errors;

 }



 $("#removeAllmanagers").bind("click", function(){
   RemoveAllmanagers();
 });

 function RemoveAllmanagers(){
 $.confirm({
   boxWidth: '30%',
   icon: 'fa fa-warning',
   theme:'light',
   closeIcon: true,
   draggable:true,
   closeIconClass: 'fa fa-close text-danger',
   title: 'Delete all managers',
   content:'Are you sure you want to remove all managers',
   buttons:{
       confirm:function(){
     var self = this;
     return $.ajax({
         data: {
             "_token": "{{ csrf_token() }}",
             },
         url: '{{ Route("suppliers.truncate") }}',
         type: 'POST',
         // dataType: 'json',
     }).done(function (data) {

         $.alert({
             title: 'Message',
             content: data.success,
         });
          $(".totl_managers").text(data.totl_no);
          $(".totl_credit").text(data.totl_credit);
          $(".totl_debt").text(data.totl_debt);
          var tbl = $('#managers-table').DataTable();
          tbl.ajax.reload();


     }).fail(function(data){
         $.alert({
             title: 'Response',
             content:"managers not deleted:"+data.fail,
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
