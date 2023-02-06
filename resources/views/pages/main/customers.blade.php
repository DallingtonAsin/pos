@extends('layouts.master')

@section('content')


      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="panel-tile">

            <div class="row nunito-font">
              <span class="response"></span>

              <div class="col-lg-2">
                <h6 class="text-dark">
                  <i class="fa fa-home text-success"> /</i>
                  <strong>Records</strong>
                  <span class="badge nunito-font  totl_customers">
                      @isset($number_of_customers)
                      {{ number_format($number_of_customers) }}
                      @endisset
                    </span>
                </h6>
              </div>


              @can('isAdmin')
              <div class="col-lg-3">
                <h5>
                  Credit: shs.<strong class="text-success totl_credit">
                      @isset($total_credit)
                      {{ number_format($total_credit) }}
                      @endisset

                    </strong>
                </h5>
              </div>

              <div class="col-lg-3">
                <h5>
                  Debts: shs.<label class="text-danger totl_debt">
                      @isset($total_debts)
                      {{ number_format($total_debts) }}
                      @endisset

                    </label>
                </h5>
              </div>
              @endcan

              <div class="col-lg-2">
                <h5>
                    <a class="text-info bolded" href="javascript:void(0)"
                     id="createNewCustomer"> Add customer</a>
                </h5>
              </div>

            @can('isAdmin')
              <div class="col-lg-2">
               <div class="btn-group">
                <button type="button" class="btn border-info text-success bolded form-control text-center dropdown-toggle downloadfilebtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Action
               </button>
               <ul class="dropdown-menu">
                   <li><a href=""  class="add-link text-dark text-decoration-none"
                     data-toggle="modal" data-target="#importCustomers"><strong>Import customers</strong>
                   </a></li>
                 <li>
                <a class="text-decoration-none text-dark
                nunito-font"
                href="javascript:void(0)"
                id="removeAllCustomers"> Delete all customers</a>
                 </li>
                </ul>
              </div>
            </div>
            @endcan

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

        <table class="table table-bordered table-hover customers-table" id="customers-table">

            <thead>
              <tr>
                 @can('isAdmin') 
                <th></th>
                @endcan
                @can('isCashier') 
                <th>No</th>
                @endcan
                {{-- <th class="td-sm">No</th> --}}
                <th>Name</th>
                <th>Contact</th>
                <th>Item taken</th>
                <th>Credit</th>
                <th>Debt</th>
                @can('isAdmin')
                <th>Added by</th>
                @endcan
                <th>Taken on</th>
                <th>Action</th>
              </tr>
            </thead>
        </table>


</div>
</div>
</div>



<!--Add customers -->
<div class="modal fade nunito-font addCustomersModal" id="addCustomersModal" tabindex="-1"
role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form name="customers" id="CustomersForm">
          @csrf
       <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="form-group">
            {{-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> --}}
            <input type="hidden" class="form-control customerId bg-white customerId" name="id"
             placeholder="Enter customer id"  Required autofocus>
          </div>

        <div class="form-group">
          <span>Name</span>
          <input type="text" class="form-control name bg-white" name="name" placeholder="Enter customer name" Required autofocus>
        </div>

       
        <div class="form-group">
          <span>Contact</span>
          <input type="text" class="form-control contact bg-white" name="contact" placeholder="Enter contact" Required autofocus>
        </div>

        <div class="form-group">
          <span>Item taken</span>
          <input name="item_taken" class="form-control item_taken" id="item_taken" placeholder="Enter item taken on credit" >
          </select>
        </div>

         <div class="form-group">
          <span>Debt</span>
          <input type="text" class="form-control debt bg-white" name="debt" placeholder="Enter debt">
        </div>

        <div class="form-group">
          <span>Credit</span>
          <input type="text" class="form-control credit bg-white" name="credit" placeholder="Enter credit">
        </div>

        <div class="form-group">
          <span>Taken on</span>
          <input type="date" value="{{ date('Y-m-d') }}" class="form-control taken_on bg-white" id="taken_on" name="taken_on">
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-primary AddcustomerBtn"  name="AddcustomerBtn">Save</button>
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

<!--Import Customers -->
<div class="modal fade nunito-font" id="importCustomers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form action="{{ Route('customers.import') }}" method="post"
      enctype="multipart/form-data" name="inportCustomersForm" >
      @csrf

      <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold">
        Import an excel file of customers </h5>
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
            <button type="submit" class="btn btn-primary">Upload</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


 <!--Modal Deletecustomers -->
 <div class="modal fade" id="deleteCustomersModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title delete-modal-title w-100 font-weight-bold">Delete customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <div class="text-center">
             <label class="text-danger delete-alert-text">
             Are you sure you want to delete this customer
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
<script>
  $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });
  const ajaxUrl = @json(route('customers.home'));
  const deletedSeletectedUrl = @json(route('selected-customers.remove'));
  const cat = 'customers';
  const token = "{{ csrf_token() }}";
  var table = $('#customers-table');
  var title = "List of registered customers in the system";
  var columns = [0, 1, 2, 3, 4];
</script>

@can('isAdmin')
<script>
  var dataColumns = [
        {data: 'checkbox', name:'checkbox'},
        //  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
         {data: 'name', name:'name'},
         {data: 'contact', name:'contact'},
         {data: 'item_taken', name:'item_taken'},
         {data: 'credit', name:'credit'},
         {data: 'debt', name:'debt'},
         {data: 'added_by', name:'added_by'},
         {data: 'taken_on', name:'taken_on'},
         {data: 'action', name: 'action',orderable: false,searchable: false},
     ];
      makeDataTable(table, title, columns, dataColumns);
</script>
@endcan

@can('isCashier')
<script>
  var dataColumns = [
        // {data: 'checkbox', name:'checkbox'},
         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
         {data: 'name', name:'name'},
         {data: 'contact', name:'contact'},
         {data: 'item_taken', name:'item_taken'},
         {data: 'credit', name:'credit'},
         {data: 'debt', name:'debt'},
         {data: 'taken_on', name:'taken_on'},
         {data: 'action', name: 'action',orderable: false,searchable: false},
     ];
      makeDataTable2(table, title, columns, dataColumns);
</script>
@endcan


<script type="text/javascript">
  $(document).ready(function(){
     $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });


            //  PopulateStockItems();


       $("#item_taken").typeahead({
        source:function(query,result){
          $.ajax({
            url:"{{ Route('stock-item.search') }}",
            method:'post',
            data:{
              query:query,
            },
            dataType:'json',
            success: function(data){
              result($.map(data, function(item){
                return item;
              }));
            },
            error:function(data){
              console.log('am not getting anything');
            },
          });
        }
      });
   
    
    

        $('#createNewCustomer').click(function (e) {
         e.preventDefault();
         DisableTableFields(false);
         ShowBtns();
        $('.AddcustomerBtn').text("Add record");
        $('.customerId').val('');
        $('#CustomersForm').trigger("reset");
        $('#modalHeading').html("Register new customer");
        $('#addCustomersModal').modal('show');
        });

  function PopulateStockItems(){
    $.ajax({
            type: "GET",
            url: "{{ route('stock.ajax.fetch') }}",
            success: function(resp){
                var obj = JSON.parse(resp);
                for(var i = 0; i < obj.length; i++) {
                    let item_id = obj[i]['id'];
                    let item_name = obj[i]['item'];
                    $('.item_taken').append('<option value=' + item_id + '>' + item_name + '</option>');
                }
            },
             error: function (data) {
              console.log('Error:', data.error);
              ShowResponse('.response', data.error, 'error');
              $('.AddcustomerBtn').html('Save Changes');
           },
        });
  }

Numberize(".debt");
Numberize(".credit");

function Numberize(i){
  $(document).on("keyup", i , function(){
  if(this.value.length > 0){
    var n = parseInt(this.value.replace(/\D/g,''), 10);
    $(this).val(n.toLocaleString());
  }
});
}

//modal used to edit customer details [each row of the tbl]
    $('body').on('click', '#edit-customer', function (event) {
      var customer_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('customers.index') }}" +'/' + customer_id +'/edit', function (data) {

          $('#modalHeading').html("Edit details of customer " + data.name + "");
          $('.AddcustomerBtn').text("Edit customer");
          $('#addCustomersModal').modal('show');
          $('.customerId').val(data.id);
          $('.name').val(data.name);
          $('.contact').val(data.contact);
          $('.item_taken').val(data.item_taken);
          $('.debt').val(data.debt);
          $('.credit').val(data.credit);
           $('.taken_on').val(data.taken_on);
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
          $('#addCustomersModal').modal('show');
          $('.customerId').val(data.id);
          $('.name').val(data.name);
          $('.contact').val(data.contact);
          $('.debt').val(data.debt);
          $('.credit').val(data.credit);
          DisableTableFields(true);
          HideBtns();
      })
   });


    $('.AddcustomerBtn').click(function (e) {

        e.preventDefault();

        var Errors = validateForm();
        if(Errors.length == 0){
        $(this).html('Sending..');

        $.ajax({
          data: $('#CustomersForm').serialize(),
          url: "{{ route('customers.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#CustomersForm').trigger("reset");
              $('#addCustomersModal').modal("hide");
              var resp = data.success;
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('#customers-table').DataTable();
              tbl.ajax.reload();

          },
          error: function (data) {
              console.log('Error:', data.error);
              ShowResponse('.response', data.error, 'error');
              $('.AddcustomerBtn').html('Save Changes');
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
            $("#deleteCustomersModal").modal('show');
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
              $('#deleteCustomersModal').modal("hide");
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('#customers-table').DataTable();
              tbl.ajax.reload();
         },
         error: function (data) {
             console.log('Error:', data);
             ShowResponse('.response', data.error, 'error');
         }
     });
 }


  function DisableTableFields(bool){

          $('.customerId').attr('disabled', bool);
          $('.name').attr('disabled', bool);
          $('.contact').attr('disabled', bool);
          $('.debt').attr('disabled', bool);
          $('.credit').attr('disabled', bool);
  }

  function HideBtns(){
          $('.AddcustomerBtn').hide();
          $('.clearBtn').hide();
          $('.closeBtn').hide();
  }

  function ShowBtns(){
          $('.AddcustomerBtn').show();
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
          var tbl = $('#customers-table').DataTable();
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
