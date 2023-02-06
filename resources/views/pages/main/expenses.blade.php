@extends('layouts.master')

@section('content')

          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="panel-tile">

                <div class="row nunito-font">
                    <div class="pl-0 mt-4 response"></div>
              <div class="col-lg-3 text-dark">
                <h6>
                  <i class="fa fa-home text-success"> /</i>
                  <strong>List of recorded Expenses</strong>
                  <span class="badge nunito-font  totl_no">
                      @isset($number_of_total_expenses)
                      {{ number_format($number_of_total_expenses) }}
                      @endisset
                    </span>
                </h6>
              </div>

              <div class="col-lg-3">
                <h5>
                  Total expenses: shs.
                  <label class="text-danger totl_amt">
                      @isset($total_expenses)
                    {{ number_format($total_expenses) }}
                      @endisset
                  </label>
                </h5>
              </div>

              <div class="col-lg-2">
                <h5>
                    <a class="text-info bolded" href="javascript:void(0)"
                     id="createNewExpense"> Add expense</a>
                </h5>
              </div>

               <div class="col-lg-2">
                <h5><a href=""  class="add-link text-decoration-none" data-toggle="modal" data-target="#importExpenses"><strong>Import Expenses</strong></a></h5>
              </div>

              <div class="col-lg-2">
               <div class="btn-group">
                <button type="button" class="btn border-info text-success bolded form-control text-center dropdown-toggle downloadfilebtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Action
               </button>
               <ul class="dropdown-menu">
               <li>
              <a class="text-decoration-none text-dark
              nunito-font"
              href="javascript:void(0)"
              id="removeAllExpenses"> Delete all expenses</a>
               </li>
            </ul>
          </div>
        </>
      </div>
    </div>
</div>
            </div>


  <div class="panel-body">
    <div class="col-lg-8 text-center">
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

                    <div class="table table-responsive custom-family">
                      <table class="table table-bordered expenses-table">

                        <thead>
                          <tr>
                            <th></th>
                            {{-- <th class="td-md">No</th> --}}
                            <th>expense</th>
                            <th>Amount</th>
                            <th>Date of Expenditure</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                    </table>
                </div>
            </div>
            </div>
          


            <!--Add expenses -->
            <div class="modal fade nunito-font" id="addExpensesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">

                  <form name="expenses" id="ExpensesForm">
                    @csrf
                    <div class="modal-header text-center">
                      <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new expense</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            {{-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> --}}
                            <input type="hidden" class="form-control expenseId bg-white expenseId" name="id"
                             placeholder="Enter expense id"  Required autofocus>
                          </div>

                      <div class="form-group">
                        <span>Expense</span>
                        <input type="text" class="form-control expense bg-white" name="expense" placeholder="Enter expense" Required autofocus>
                      </div>

                      <div class="form-group">
                        <span>Amount</span>
                        <input type="text" class="form-control amount bg-white" name="expenditure_amount" placeholder="Amount in shs." Required autofocus>

                      </div>

                      <div class="form-group">
                        <span>Date</span>
                        <input type="date" class="form-control date bg-white" value="{{ date('Y-m-d') }}" name="date_of_expense" placeholder="Enter cost of expense" Required autofocus>
                      </div>

                      <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="addExpensesBtn"  name="AddExpenseBtn">Save</button>
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

            <!--Import Expenses -->
            <div class="modal fade nunito-font" id="importExpenses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">

                  <form action="{{ Route('expenses.import') }}" method="post"
                  enctype="multipart/form-data" name="inportExpensesForm" >
                  @csrf

                  <div class="modal-header text-center">
                    <h5 class="modal-title w-100 font-weight-bold">Import an excel file of expenses</h5>
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
                        <button type="submit" class="btn btn-primary"  name="AddItemBtn">Upload</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      </div>


                    </div>
                  </form>
                </div>
              </div>
            </div>


            <!--Modal Deleteexpenses -->
 <div class="modal fade" id="deleteExpensesModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title delete-modal-title w-100 font-weight-bold">Delete expense</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <div class="text-center">
             <label class="text-danger delete-alert-text">
                 Are you sure you want to delete this expense?

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
</div> <!-- end of modal DeleteExpenses-->

    <script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
<script>
  const ajaxUrl = @json(route('get-expenses'));
  const deletedSeletectedUrl = @json(route('selected-expenses.remove'));
  const cat = 'expenses';
  const token = "{{ csrf_token() }}";
</script>
<script type="text/javascript">

  $(document).ready(function(){
     $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
         });

     //code that displays results of the table index()
    var table = $('.expenses-table');
    var title = "List of recorded expenses in the system";
    var columns = [0, 1, 2, 3];
    var dataColumns = [
        {data: 'checkbox', name:'checkbox'},
        //  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
         {data: 'expense_type', name:'expense'},
         {data: 'amount', name:'amount'},
         {data: 'date_of_expenditure', name:'date-of_expenditure'},
         {data: 'action', name: 'action',orderable: false,searchable: false},
     ];
    
     makeDataTable(table, title, columns, dataColumns);

      $('#createNewExpense').click(function (e) {
         e.preventDefault();
         DisableFormFields(false);
         ShowBtns();
        $('#addExpensesBtn').text("Record expense");
        $('.expenseId').val('');
        $('#ExpensesForm').trigger("reset");
        $('#modalHeading').html("Record new expense");
        $('#addExpensesModal').modal('show');
       });

function SanitizeString(str){
  var newStr = str.replace(/,/g , '').trim();
  return newStr;
}

Numberize(".amount");

function Numberize(i){
  $(document).on("keyup", i , function(){
  if(this.value.length > 0){
    var n = parseInt(this.value.replace(/\D/g,''), 10);
    $(this).val(n.toLocaleString());
  }
});
}

 function FormatNumber(number){
   var FormattedNumber = parseFloat(number).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 0});
   return FormattedNumber;
  }


//modal used to edit expenses details [each row of the tbl]
    $('body').on('click', '#edit-expense', function (event) {
      var expense_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('expenses.index') }}" +'/' + expense_id +'/edit', function (data) {

          $('#modalHeading').html("Edit details of expense " + data.expense_type + "");
          $('#addExpensesBtn').text("Edit expense");
          $('#addExpensesModal').modal('show');
          $('.expenseId').val(data.id);
          $('.expense').val(data.expense_type);
          $('.amount').val(data.amount);
          $('.date').val(data.date_of_expenditure);
          DisableFormFields(false);
          ShowBtns();
      })
   });


   //View Modal used to view each row [expenses details]
   $('body').on('click', '#view-expense', function (event) {
      var expense_id = $(this).data('id');
      event.preventDefault();

      $.get("{{ route('expenses.index') }}" +'/' + expense_id +'', function (data) {

          $('#modalHeading').html("Details of expense " + data.expense_type + "");
          $('#addExpensesModal').modal('show');
          $('.expenseId').val(data.id);
          $('.expense').val(data.expense_type);
          $('.amount').val(data.amount);
          $('.date').val(data.date_of_expenditure);
          DisableFormFields(true);
          HideBtns();
      })
   });





    $('#addExpensesBtn').click(function (e) {

        e.preventDefault();
        var Errors = validateForm();

        if(Errors.length == 0){

        $('.errors-section').html('');
        $(this).html('Sending..');

        $.ajax({
          data: $('#ExpensesForm').serialize(),
          url: "{{ route('expenses.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#ExpensesForm').trigger("reset");
              $('#addExpensesModal').modal("hide");
              var resp = data.success;
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('.expenses-table').DataTable();
              tbl.ajax.reload();

          },
          error: function (data) {
              console.log('Error:', data.error);
              ShowResponse('.response', data.error, 'error');
              $('#addExpensesBtn').html('Save Changes');
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
    $('body').on('click', '#delete-expense', function (e) {
            var expense_id = $(this).data("id");
            e.preventDefault();
            $("#deleteExpensesModal").modal('show');
            $(".delete-alert-text").html("Are you sure you want to delete this expense?");
            $('.delete-ok-btn').on('click', function(){
                   ListenAndDoDeletion(expense_id);
         });

 });


 function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("expenses.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
     $('.delete-ok-btn').html('Deleting...');
        $.ajax({
         type: "DELETE",
         url: deleteUrl,
         success: function (data) {
              var resp = data.success;
              $('.delete-ok-btn').html('Yes');
              $('#deleteExpensesModal').modal("hide");
              ShowResponse('.response', resp, 'success');
              ResetTblInfo(data);
              var tbl = $('.expenses-table').DataTable();
              tbl.ajax.reload();
         },
         error: function (data) {
             console.log('Error:', data);
             ShowResponse('.response', data.error, 'error');
         }
     });
 }




  function DisableFormFields(bool){
          $('.expense').attr('disabled', bool);
          $('.amount').attr('disabled', bool);
          $('.date').attr('disabled', bool);
  }

  function HideBtns(){
          $('#addExpensesBtn').hide();
          $('.clearBtn').hide();
          $('.closeBtn').hide();
  }

  function ShowBtns(){
          $('#addExpensesBtn').show();
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
     var totl_amt, totl_no;
     totl_no = FormatNumber(response.totl_no);
     totl_amt = FormatNumber(response.totl_expenses);
     $('.totl_no').html(totl_no)
     $('.totl_amt').html(totl_amt);
 }

 function validateForm()
 {
    var expense = $('.expense').val();
    var amt = $('.amount').val();
    var when = $('.date').val();
    var errors = [];
    if(expense.length < 1){
      var expenseErr = "Please enter the name of the expense";
      errors.push(expenseErr);
    }
    if(!amt){
      var amtErr = "Please enter the amount";
      errors.push(amtErr);
    }

    if(!Date.parse(when)){
     var dateErr = "Please enter a valid date of expenditure";
     errors.push(dateErr);
    }

      return errors;

 }

 function isValidDate(value) {
    var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
    var flag = re.test(value);
    return flag;
}


 $("#removeAllExpenses").bind("click", function(){
   RemoveAllExpenses();
 });

 function RemoveAllExpenses(){
 $.confirm({
   boxWidth: '30%',
   icon: 'fa fa-warning',
   theme:'light',
   closeIcon: true,
   draggable:true,
   closeIconClass: 'fa fa-close text-danger',
   title: 'Delete all expenses',
   content:'Are you sure you want to remove all expenses',
   buttons:{
       confirm:function(){
     var self = this;
     return $.ajax({
         data: {
             "_token": "{{ csrf_token() }}",
             },
         url: '{{ Route("expenses.truncate") }}',
         type: 'POST',
         // dataType: 'json',
     }).done(function (data) {

         $.alert({
             title: 'Message',
             content: data.success,
         });
          $(".totl_no").text(data.totl_no);
          $(".totl_amt").text(data.totl_expenses);
          var tbl = $('.expenses-table').DataTable();
          tbl.ajax.reload();


     }).fail(function(data){
         $.alert({
             title: 'Response',
             content:"Expenses not deleted:"+data.fail,
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
