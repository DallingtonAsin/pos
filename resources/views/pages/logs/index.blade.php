@extends('layouts.master')

@section('content')


          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="panel-tile">
                <div class="row nunito-font">
            <div class="col-lg-10">
              <h6 class="nunito-font">
                <i class="fa fa-home text-success"></i> /
                <span class="text-dark">
                  <strong>Activity Logs</strong>
                   <span class="badge totl_logs">
                  @isset($number_of_logs)
                  {{ number_format($number_of_logs) }}
                  @endisset
                  </span>
                </span>
              </h6>
            </div>

            {{-- @can('isAdmin')
            <div class="col-lg-2">

             <a class="text-decoration-none text-danger
             nunito-font"  href="javascript:void(0)" id="removeAllLogs">
            <i class="fa fa-trash-alt"></i> Delete all logs</a>

          </div>
          @endcan --}}
        </div>
              </div>
    </div>

    <div class="panel-body">

        <div class="col-lg-8 text-center">
         @if(session()->get('log-deleted'))
         <div class='alert alert-success alert-dismissible text-center' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Well done!</strong> {{ session()->get('log-deleted') }}
          </div>
          @endif

          @if(session()->get('log-not-deleted'))
          <div class='alert alert-danger alert-dismissible text-center' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Opps!</strong> {{ session()->get('log-not-deleted') }}
          </div>
          @endif
        </div>


        <div class="table table-responsive custom-family">
          <table class="table logs-table table-bordered table-hover" id="logs-table">
            <thead>
              <tr>
                <th>No</th>
                <th>User</th>
                <th>Role</th>
                <th>Log</th>
                <th>Ip Address</th>
                <th>Done on</th>
                <th>Action</th>
              </tr>
            </thead>
      </table>
      </div>

  <!--Modal DeleteLog -->
<div class="modal fade pt-5" id="deleteLogsModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content nunito-font border border-custom-dark rounded-0">
        <div class="modal-header main-color-bg  text-center">
          <h5 class="modal-title w-100 text-white font-weight-bold">
             Delete Log</h5>
          <button type="button" class="close view-close" data-dismiss="modal" aria-label="Close ">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <div class="text-center">
             <label class="text-danger">Are you sure you want to delete this log?
           </label>
         </div>
       </div>

       <div class="form-group">
         <form>
          @csrf
          <button type="submit" class="btn btn-success delete-ok-btn"  name="ConfirmBtn">Yes</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div> <!-- end of modal DeleteLog-->




        <!-- View Log Details -->
        <div class="modal fade" id="LogsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

            <div class="modal-content nunito-font border border-custom-dark rounded-0">
              <div class="modal-header  text-center">
                <h5 class="modal-title w-100 nunito-font text-white  font-weight-bold">
                  <i class="fa fa-info-circle"></i>
                  Details of a logged activity
                </h5>
                <button type="button" class="close view-close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">

                <div class="modal-body">
                  <div class="form-group">
                    <span class="text-left">User</span>
                    <input type="text" class="form-control user  text-dark" value="" readonly>
                  </div>

                  <div class="form-group">
                    <span class="text-left">Role</span>
                    <input type="text" class="form-control role  text-dark"  value="" readonly>

                  </div>

                  <div class="form-group">
                    <span class="text-left">Action</span>
                    <textarea class="form-control  action text-dark" readonly></textarea>

                  </div>

                  <div class="form-group">
                    <span class="text-left">Ip Address</span>
                    <input type="text" class="form-control ipAddress  text-dark"  value="" readonly>
                  </div>

                  <div class="form-group">
                    <span class="text-left">Done on</span>
                    <input type="text" class="form-control date  text-dark"  value="" readonly>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>



    </div>
  </div>


<script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('vendors/notify/notify.js') }}"></script>
<script>
  const ajaxUrl = @json(route('get-logs'));
</script>


<script type="text/javascript">

$(document).ready(function(){
 $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
     });



// code that displays results of the table index()
    var table = $('.logs-table');
    var title = "List of logs in the system";
    var columns = [0, 1, 2, 3 ,4];
    var dataColumns = [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
     {data: 'name', name:'name'},
     {data: 'role', name:'role'},
     {data: 'logged_action', name:'logged_action'},
     {data: 'ip_address', name:'ip_address'},
     {data: 'date', name:'date'},
     {data: 'action', name: 'action',orderable: false,searchable: false},
 ];
 makeDataTable2(table, title, columns, dataColumns);

//View Modal used to view each row [logs details]
$('body').on('click', '#view-log', function (event) {
  var log_id = $(this).data('id');
  event.preventDefault();

  $.get("{{ route('logs.index') }}" +'/' + log_id +'', function (data) {
      $('#LogsModal').modal('show');
      $('.user').val(data.name);
      $('.role').val(data.role);
      $('.action').html(data.logged_action);
      $('.ipAddress').val(data.ip_address);
      $('.date').val(data.date);
      DisableTableFields(true);
      HideBtns();
  })
});


//this pops up confirm delete modal
$('body').on('click', '#delete-log', function (e) {
        var log_id = $(this).data("id");
        e.preventDefault();
        $("#deleteLogsModal").modal('show');
        $('.delete-ok-btn').on('click', function(){
               ListenAndDoDeletion(log_id);
     });

});


function ListenAndDoDeletion(id){
var deleteUrl = '{{ route("logs.destroy", ":id") }}';
deleteUrl = deleteUrl.replace(':id', id);
 $('.delete-ok-btn').html('Deleting...');
    $.ajax({
     type: "DELETE",
     url: deleteUrl,
     success: function (data) {
          var resp = data.success;
          $('.delete-ok-btn').html('Yes');
          $('#deleteLogsModal').modal("hide");
          ShowResponse('.response', resp, 'success');
          ResetTblInfo(data);
          var tbl = $('.logs-table').DataTable();
          tbl.ajax.reload();
     },
     error: function (data) {
         console.log('Error:', data);
         ShowResponse('.response', data.error, 'error');
     }
 });
}

function ResetTblInfo(response)
 {
     var totl_logs;
     totl_logs = FormatNumber(response.totl);
     $('.totl_logs').html(totl_logs);
 }

 function FormatNumber(number){
   var FormattedNumber = parseFloat(number).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 0});
   return FormattedNumber;
  }


function DisableTableFields(bool){
      $('.log').attr('disabled', bool);
      $('.amount').attr('disabled', bool);
      $('.date').attr('disabled', bool);
}

function HideBtns(){
      $('#addlogsBtn').hide();
      $('.clearBtn').hide();
      $('.closeBtn').hide();
}

function ShowBtns(){
      $('#addlogsBtn').show();
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
});

$("#removeAllLogs").bind("click", function(){
  RemoveAllLogs();
});

function RemoveAllLogs(){
$.confirm({
  boxWidth: '30%',
  icon: 'fa fa-warning',
  theme:'light',
  closeIcon: true,
  draggable:true,
  closeIconClass: 'fa fa-close text-danger',
  title: 'Delete all logs',
  content:'Are you sure you want to delete all logs',
  buttons:{
      confirm:function(){
    var self = this;
    return $.ajax({
        data: {
            "_token": "{{ csrf_token() }}",
            },
        url: '{{ Route("logs.truncate") }}',
        type: 'POST',
    }).done(function (data) {
         var resp = data.success;
         $(".totl_logs").text(data.totl);
         var tbl = $('.logs-table').DataTable();
         tbl.ajax.reload();
         ShowResponse('.response', resp, 'success');
    }).fail(function(error){
        $.alert({
            title: 'Response',
            content:error.fail,
        });
        console.log(error);
    });

      },
      cancel:function(){

      }
  },
});

  }




</script>

@endsection
