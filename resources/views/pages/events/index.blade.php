@extends('layouts.master')

@section('content')

<div class="panel panel-default">

  <div class="panel-heading">
    <div class="panel-title">
      <div class="row nunito-font">
        <div class="col-lg-4 text-dark">
          <h6>
            <i class="fa fa-home text-success"> /</i>
            <strong>Upcoming Events</strong>
            @isset($no_of_events)
            <span class="badge totl_no">
              {{ number_format($no_of_events) }}
            </span>
            @endisset

          </h6>
        </div>
      </div>
    </div>
  </div>

  <div class="panel-body">
    <span class="response"></span>
    <div class="row">
      <div class="col-lg-8 text-center nunito-font">
        @if(session()->get('success'))
        <div class='alert alert-success alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Yello!</strong> {{ session()->get('success') }}<i
            class="fa fa-check-circle"></i>
          </div>
          @endif

          @if(session()->get('fail'))
          <div class='alert alert-success alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span></button>
              <strong>Yello!</strong> {{ session()->get('fail') }}
            </div>
            @endif
          </div>
        </div>

        <div class="table table-responsive custom-family ">
          <table class="table table-bordered" id="eventsTable">
            <thead>
              <tr>
                <th>No</th>
                <th>Event Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Start Time</th>
                <th>RecordedBy</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>


        <!-- View Event Details -->
        <div class="modal fade" id="viewEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

            <div class="modal-content nunito-font border border-custom-dark rounded-0">
              <div class="modal-header  text-center">
                <h5 class="modal-title w-100 nunito-font text-white modalViewEventHeading  font-weight-bold">
                  <i class="fa fa-info-circle "></i>
                  Details of an event 
                </h5>
                <button type="button" class="close view-close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">

                <form id="EventForm">

                  <div class="modal-body">
                    <div class="form-group">
                      <span>Title</span>
                      <input type="hidden" name="eventId"  class="eventId" />
                      <input type="text" name="event-title" class="form-control title  text-dark" value="" >
                    </div>

                    <div class="form-group">
                      <span>Details</span>
                      <textarea name="event-message" class="form-control details  text-dark" ></textarea>
                    </div>

                    <div class="form-group">
                      <span>Start Date</span>
                      <input type="date" name="eventStart-date" class="form-control start_date  text-dark"  value="" >
                    </div>

                    <div class="form-group">
                      <span>End Date</span>
                      <input type="date" name="eventEnd-date" class="form-control end_date  text-dark"  value="" >
                    </div>

                    <div class="form-group">
                      <span>Start Time</span>
                      <input type="text" name="event-time" class="form-control start_time  text-dark"  
                      value="" >
                    </div>

                    <div class="form-group">
                      <span>Recorded by</span>
                      <input type="text" name="registra" class="form-control recordedBy  text-dark" readonly value="" >
                    </div>

                    <div class="form-group">
                      <button type="submit" class="btn btn-success addEventBtn"  name="AddItemBtn">Save</button>
                      <button type="reset" class="btn btn-danger clearBtn">Clear</button>
                      
                    </div>

                    <div class="form-group">
                      <span class="errors-section text-danger nunito-font"></span>    
                    </div>

                  </div>
                </form>
              </div>


            </div>
          </div>
        </div>  <!-- end of modal ViewEvent-->


        
        <!--Modal DeleteEvent -->
        <div class="modal fade" id="deleteEventModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header text-center">
                <h5 class="modal-title w-100 font-weight-bold">Delete Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">

                <div class="form-group">
                  <div class="text-center">
                   <label class="text-danger">
                     Are you sure you want to delete an event titled 
                     <span class="text-dark text-muted bolded event-to-delete">

                     </span>?
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
      </div> <!-- end of modal DeleteEvent-->
    </div>
  </div>

  <script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
  <script src="{{ asset('vendors/notify/notify.js') }}"></script>
  <script>
    const ajaxUrl = @json(route('get-events'));
  </script>
  <script type="text/javascript">

    $(document).ready(function(){
     $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

  onClickSubmitBtn();


     // code that displays results of the table index()
     var table = $('#eventsTable');
     var title = "List of events in the system";
     var columns = [0, 1, 2, 3 ,4, 5];
     var dataColumns = [
     {data: 'id', name:'id'},
     {data: 'title', name:'title'},
     {data: 'start_date', name:'start_date'},
     {data: 'end_date', name:'end_date'},
     {data: 'start_time', name:'start_time'},
     {data: 'event_registra', name:'event_registra'},
     {data: 'action', name:'action',orderable: false,searchable: false},
     ];
     makeDataTable2(table, title, columns, dataColumns);

     function UpdateEvent(event_id){

      $('.errors-section').html('');
      $('.addEventBtn').html('Updating event...');

      var Url = "{{ route('events.update', ':id') }}";
      Url = Url.replace(':id', event_id);
      $.ajax({
        data: $('#EventForm').serialize(),
        url: Url,
        type: "PUT",
        dataType: 'json',
        success: function (data) {

          $('#EventForm').trigger("reset");
          $('#viewEventModal').modal("hide");
          var resp = data.success;
          ShowResponse('.response', resp, 'success');
          ResetTblInfo(data);
          var tbl = $('#eventsTable').DataTable();
          tbl.ajax.reload();

        },
        error: function (data) {
          console.log('Error:', data);
          ShowResponse('.response', data, 'error');
          $('.addEventBtn').html('Save Changes');
        }
      });
    }

    function onClickSubmitBtn(){
      $('.addEventBtn').click(function (e) {
        var id = $(".eventId").val();
        e.preventDefault();
        var Errors = validateForm();
        if(Errors.length == 0){
          UpdateEvent(id); 
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
    }

   //modal used to edit event details [each row of the tbl]
   $('body').on('click', '#edit-event', function (event) {
    var event_id = $(this).data('id');
    event.preventDefault();
    DisableFormFields(false);
    ShowHideBtns('show');
    $('.addEventBtn').text("Edit event");
    $('#viewEventModal').modal('show');
    var Url = "{{ route('events.show', ':id') }}";
    Url = Url.replace(':id', event_id);
    $.ajax({

      url: Url,
      type: "GET",
      dataType: 'json',
      success: function (data) {

        $('.modalViewEventHeading').html("Edit details of event titled " + data.title + "");
        $('.eventId').val(data.id);
        $('.title').val(data.title);
        $('.details').val(data.description);
        $('.start_date').val(data.start_date);
        $('.end_date').val(data.end_date);
        $('.start_time').val(data.start_time);
        $('.recordedBy').val(data.event_registra);

      },
      error: function (data) {
        console.log('Error:', data.error);
        ShowResponse('.response', data.error, 'error');
      }
    });

  });


   //View Modal used to view each row [event details]
   $('body').on('click', '#view-event', function (event) {
    var event_id = $(this).data('id');
    ShowHideBtns('hide');
    var showUrl = '{{ route("events.show", ":id") }}';
    showUrl = showUrl.replace(":id", event_id);
    event.preventDefault();
    $.ajax({

      url: showUrl,
      type: 'GET', 
      dataType: 'json',
      success: function(data){

        $('.modalViewEventHeading').html("Details of event titled " + data.title + "");
        $('#viewEventModal').modal('show');
        $('.title').val(data.title);
        $('.details').val(data.description);
        $('.start_date').val(data.start_date);
        $('.end_date').val(data.end_date);
        $('.start_time').val(data.start_time);
        $('.recordedBy').val(data.event_registra);
        DisableFormFields(true);

      },
      error: function(data)
      {
       console.log(data);
     }


   });

  });

   //this pops up confirm delete modal
   $('body').on('click', '#delete-event', function (e) {
    var event_id = $(this).data("id");
    GetEventTitle(event_id);
    e.preventDefault();
    $("#deleteEventModal").modal('show');
    $('.delete-ok-btn').on('click', function(){
      ListenAndDoDeletion(event_id);
    });

  });

   function GetEventTitle(id){

     var getUrl = '{{ route("getEventTitle", ":id") }}';
     getUrl = getUrl.replace(':id', id);
     $.ajax({
      type: "GET",
      url: getUrl,
      success: function (data) {
        $('.event-to-delete').html(data.title);
      },
      error: function (data) {
        console.log('Error:', data);    
      }
    });

   }



   function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("events.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
    $('.delete-ok-btn').html('Deleting...');
    $.ajax({
     type: "DELETE",
     url: deleteUrl,
     success: function (data) {
      var resp = data.success;
      $('.delete-ok-btn').html('Yes');
      $('#deleteEventModal').modal("hide");
      ShowResponse('.response', resp, 'success');
      ResetTblInfo(data);
      var tbl = $('#eventsTable').DataTable();
      tbl.ajax.reload();
    },
    error: function (data) {
     console.log('Error:', data);
     ShowResponse('.response', data.error, 'error');
   }
 });
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

  
  function ResetTblInfo(response)
  {
   var totl_no;
   totl_no = FormatNumber(response.totl_no);
   $('.totl_no').html( totl_no);
 }

 function FormatNumber(number)
 {
   var FormattedNumber = parseFloat(number).toLocaleString('us', {minimumFractionDigits: 0, maximumFractionDigits: 0});
   return FormattedNumber;
 }

 function DisableFormFields(bool){

  $('.eventId').attr('disabled', bool);
  $('.title').attr('disabled', bool);
  $('.details').attr('disabled', bool);
  $('.start_date').attr('disabled', bool);
  $('.end_date').attr('disabled', bool);
  $('.start_time').attr('disabled', bool);
  $('.recordedBy').attr('disabled', bool);

}

function ShowHideBtns(action){

  if(action == 'hide'){
    $('.addEventBtn').hide();
    $('.clearBtn').hide();
    $('.closeBtn').hide();
  }else if(action == 'show'){
    $('.addEventBtn').show();
    $('.clearBtn').show();
    $('.closeBtn').show();
  }
}

function validateForm()
{
  var title = $('.title').val();
  var details = $('.details').val();
  var start_date = $('.start_date').val();
  var end_date = $('.end_date').val();
  var start_time = $('.start_time').val();
  var registra = $('.recordedBy').val();

  var errors = [];
  if(title.length < 1){
    var titleErr = "Please enter title of the event";
    errors.push(titleErr);
  }
  if(details.length < 1){
    var detailsErr = "Please enter details of the event";
    errors.push(detailsErr);
  }
  if(start_date == ""){
   var start_dateErr = "Please enter valid start date of the event";
   errors.push(start_dateErr);
 }
 if(end_date == ""){
   var end_dateErr = "Please enter valid end date of the event";
   errors.push(end_dateErr);
 }
 if(start_time == ""){
   var start_timeErr = "Please enter start time of the event";
   errors.push(start_timeErr);
 }
 if(registra == ""){
   var registraErr = "Please enter valid name of who is registering event";
   errors.push(registraErr);
 }
 return errors;
}


});

</script>

@endsection
