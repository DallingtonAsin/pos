@extends('layouts.master')

@section('content')

<div class="panel panel-success">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
    <div class="row nunito-font">
      <span class="response"></span>

      @can('isAdmin')
      <div class="col-lg-3">
        <label>Sales</label>
        <span class="badge nunito-font totl_no">
         @isset($totl_no)
         {{ number_format($totl_no) }}
         @endisset

         @isset($totl_filtered)
         {{ number_format($totl_filtered) }}
         @endisset
       </span>
     </div>
     @endcan

     @can('isCashier')
     @isset($volume_of_todaysales)
     <div class="col-lg-3 today-amount">
       <span>
         <h5>
          Today: shs.
          <strong class="text-success volume">{{ number_format($volume_of_todaysales) }}</strong>
        </h5>
      </span>
    </div>
    <div class="col-lg-3 amount hidden">
     <label>Net Value:</label>
     <strong class="net_value">
      {{ number_format(0) }}
    </strong>
  </div>
  @endisset
  @endcan

  @can('isAdmin')

  @isset($total_sales)
  <div class="col-lg-3 amount">
    <label>Sales made: shs.</label>
    <strong class="text-success totl_sales">{{ number_format($total_sales) }}</strong>
  </div>
  @endisset




  @isset($netValue)

  <div class="col-lg-3 amount">
   <label>Net Value:</label>
   <strong class="net_value">
    {{ number_format($netValue) }}
  </strong>
</div>

@endisset
@endcan

@cannot('isCashier')
<div class="col-lg-3">
 <small>
   <a href="{{ Route('sales.index')}}" class="text-info bolded">Load all</a>
 </small>
</div>
@endcannot

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

      @if(session('fail'))
      <div class='alert alert-danger alert-dismissible' role='alert'>
       <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <span aria-hidden='true'>&times;</span></button>
        <strong>Sorry!</strong> {{ session('fail') }}
      </div>
      @endif

    </div>
  </div>

  <form action="{{ Route('filtersales') }}" method="POST"> 
   <div class="row nunito-font">
    <div class=" form-group col-md-3">
     <input type="date" name="start_date" class="form-control start_date custom-family">
   </div>
   <div class="form-group col-md-3">
    <input type="date" name="end_date" class="form-control end_date  custom-family">
  </div>
  <div class="col-md-4">
   <button type="button" class="btn btn-sm btn-success filterSalesBtn">Filter sales</button>
 </div>
</div>
</form>

<div class="table-responsive custom-family">
  <table class="table table-bordered sales-table" id="sales-table">
    <thead>
      <tr>
        @can('isAdmin') 
        <th></th>
        @endcan
        @can('isCashier') 
        <th>No</th>
        @endcan
        <th>Item</th>
        <th>Qty</th>
        <th>S. Price</th>
        <th>Disc</th>
        <th>Total</th>
        <th>Amount</th>
        <th>Bal.</th>
        <th>Customer</th>
        <th>Workedon By</th>
        <th>Date</th>
        @can('isAdmin') 
        <th>Action</th>
        @endcan
        

      </tr>
    </thead>
    
    <tbody>
    </tbody>
  </table>
</div>


<!--Modal DeleteSale -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold">Delete sold item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <div class="text-center">
           <label class="text-danger delete-modalHeading">
             Are you sure you want to delete sold item
             <strong class="item-to-delete text-dark"></strong>?
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
</div> <!-- end of modal DeleteSale-->



<!-- View Sold Item Details -->
<div class="modal fade" id="SalesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

    <div class="modal-content nunito-font border border-custom-dark rounded-0">
      <div class="modal-header  text-center">
        <h5 class="modal-title w-100 nunito-font text-dark modalHeading font-weight-bold">
          <i class="fa fa-info-circle"></i>
          Details of the sale
        </h5>
        <button type="button" class="close view-close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <form name="SaleForm" id="SaleForm">
         @csrf
         <div class="modal-body">
          <div class="form-group">
            <span>Item</span>
            <input type="hidden" class="form-control item_id" name="item_id">
            <input type="text" class="form-control item-name messageArray text-dark" value="">
          </div>

          <div class="form-group">
            <span>Qty</span>
            <input type="text" class="form-control qty messageArray text-dark"  value="">
          </div>

          <div class="form-group">
            <span>Selling Price</span>
            <input type="text" class="form-control sprice messageArray text-dark"  value="">
          </div>

          <div class="form-group">
            <span>Total cost</span>
            <input type="text" class="form-control tcost messageArray text-dark"  value="">
          </div>
          
          <div class="form-group row">

            <div class="col-lg-6">
              <span>Discount</span>
              <input type="text" class="form-control discount messageArray text-dark"  value="">
            </div>

            <div class="col-lg-6">
              <span>Sold at</span>
              <input type="text" class="form-control amount messageArray text-danger"  value="">
            </div>

          </div>



          <div class="form-group row">

            <div class="col-lg-6">
              <span>Customer</span>
              <input type="text" class="form-control customer messageArray text-dark"  value="">
            </div>

            <div class="col-lg-6">
              <span>Cashier</span>
              <input type="text" class="form-control cashier messageArray text-dark"  value="">
            </div>

          </div>

          <div class="form-group">
            <span>Date of transaction</span>
            <input type="date" name="date_of_sale" class="form-control date messageArray text-dark"  value="">
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-success addSaleBtn"  name="AddItemBtn">Save</button>
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
</div>  <!-- end of modal ViewSale-->
</div>
</div>


<script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('vendors/notify/notify.js') }}"></script>

<script>
  $(document).ready(function(){

    $('.clock-time-x').click(function(){
     $('.time1').val('');
   });
    $('.clock-time-y').click(function(){
      $('.time2').val('');
    });
    $('.time1').wickedpicker({
      now: '00:00',
      twentyFour:true,
    });
    $('.time2').wickedpicker({
      now: '00:00',
      twentyFour:true,
    });
  });

</script>

@isset($filtered_sales)
<script type="text/javascript">

  $(document).ready(function(){
    var table = $(".sales-table");
    var title = "List of filtered sales";
    var columns = [0,1,2,3,4,5,6,7,8];
    smartTable(table, title,columns);
  });
</script>
@endisset



<script>
 $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

 const deletedSeletectedUrl = @json(route('selected-sales.remove'));
 const cat = 'sales';
 const token = "{{ csrf_token() }}";

 var table = $('.sales-table');
 var title = "List of sales items in the system";
 var columns = [0,1,2,3,4,5,6,7, 8];

</script>

@can('isAdmin')
<script>
 const ajaxUrl =   @json(route('get-sales'));
 var dataColumns = [
 {data: 'checkbox', name:'checkbox'},
        //  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
        //  {data: 'item_id', name:'item_id'},
        {data: 'item', name:'item'},
        {data: 'quantity', name:'quantity'},
        {data: 'selling_price', name:'selling_price'},
        {data: 'discount', name:'discount'},
        {data: 'amount', name:'amount'},
        {data: 'paid_amount', name:'paid_amount'},
        {data: 'balance', name:'balance'},
        {data: 'customer', name:'customer'},
        {data: 'workedon_by', name:'workedon_by'},
        {data: 'date', name:'date'},
        {data: 'action', name:'action',orderable: false,searchable: false},
        ];
        makeDataTable(table, title, columns, dataColumns);
      </script>
      @endcan

      @can('isCashier')
      <script>
       const ajaxUrl =   @json(route('get-daily-sales'));
       var dataColumns = [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
        {data: 'item', name:'item'},
        {data: 'quantity', name:'quantity'},
        {data: 'selling_price', name:'selling_price'},
        {data: 'discount', name:'discount'},
        {data: 'amount', name:'amount'},
        {data: 'paid_amount', name:'paid_amount'},
        {data: 'balance', name:'balance'},
        {data: 'customer', name:'customer'},
        {data: 'workedon_by', name:'workedon_by'},
        {data: 'date', name:'date'},
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

         changeNetValueClass();
         onClickSubmitBtn();


         function SanitizeString(str){
           let newStr = str;
          if(str){
            newStr = str.replace(/,/g , '').trim();
          }
          return newStr;
        }

        function changeNetValueClass(){
          let netValue =  $('.net_value').html();
          netValue = SanitizeString(netValue);

          if(netValue > 0){
            $('.net_value').removeClass('text-danger');
            $('.net_value').addClass('text-success');
          }else{
           $('.net_value').removeClass('text-success');
           $('.net_value').addClass('text-danger');
         }
       }


       $('.filterSalesBtn').on('click', function(){

        var datatable = $('.sales-table').DataTable();
        var from = $('.start_date').val();
        var to = $('.end_date').val();
        var Url = "{{ route('filtersales') }}";
        $.ajax({

          url: Url,
          type: "POST",
          data:{
           _token:'{{ csrf_token() }}',
           from: from,
           to: to,
         },
         success: function (resp) {

          var data = resp.data;
          var totl_filtered = resp.totl_filtered;
          var totl_volume = resp.volume;
          var netValue = resp.netValue;

          $('.totl_no').html(FormatNumber(totl_filtered));
          $('.totl_sales').html(FormatNumber(totl_volume));
          $('.net_value').html(FormatNumber(netValue));
          changeNetValueClass();
          console.log("Data", data);
          console.log("Total filtered", totl_filtered);
          console.log("Total volume", totl_volume);
          console.log("Net value", netValue);

          datatable.clear();
          datatable.rows.add(data);
          datatable.draw();
        },
        drawCallback: function(extra){
         console.log("More data here", datatable.ajax.json());
       },
       error: function (xhr, status, error) {
        console.log('Error:', error);

      }
    });
      });

   //View Modal used to view each row [sale details]
   $('body').on('click', '#view-sale', function (event) {
    var sale_id = $(this).data('id');
    event.preventDefault();
    ShowHideBtns('hide');
    $.get("{{ route('sales.index') }}" +'/' + sale_id +'', function (data) {
      var bprice = data.buying_price;
      var sprice = data.selling_price;
      $('.modalHeading').html("Details of sale " + data.item + "");
      $('#SalesModal').modal('show');
      $('.item_id').val(data.id);
      $('.item-name').val(data.item);
      $('.qty').val(data.quantity);
      $('.sprice').val(FormatNumber(data.selling_price));
      $('.tcost').val(FormatNumber(data.total_cost));
      $('.discount').val(FormatNumber(data.discount));
      $('.amount').val(FormatNumber(data.amount));
      $('.customer').val(data.customer);
      $('.cashier').val(data.cashier);
      $('.date').val(data.date);
      DisableTableFields(true, true);

    });
  });

   //modal used to edit sale
   $('body').on('click', '#edit-sale', function (event) {
    var sale_id = $(this).data('id');
    var itemName = $(this).data('item');
    event.preventDefault();

    $('.modalHeading').html("Edit details of stock item " + itemName + "");

    ShowHideBtns('show');
    $('.addSaleBtn').text("Update");
    $('#SalesModal').modal('show');
    var Url = "{{ route('sales.show', ':id') }}";
    Url = Url.replace(':id', sale_id);
    $.ajax({

      url: Url,
      type: "GET",
      dataType: 'json',
      success: function (data) {
        $('#SalesModal').modal('show');
        var bprice = data.buying_price;
        var sprice = data.selling_price;
        $('.item_id').val(data.id);
        $('.item-name').val(data.item);
        $('.qty').val(data.quantity);
        $('.sprice').val(FormatNumber(data.selling_price));
        $('.tcost').val(FormatNumber(data.total_cost));
        $('.discount').val(FormatNumber(data.discount));
        $('.amount').val(FormatNumber(data.amount));
        $('.customer').val(data.customer);
        $('.cashier').val(data.cashier);
        $('.date').val(data.date);
        DisableTableFields(true, false);
      },

      error: function (data) {
        console.log('Error:', data.error);
        ShowResponse('.response', data.error, 'error');
      }
    });

  });

   //this pops up confirm delete modal
   $('body').on('click', '#delete-sale', function (e) {
    var sale_id = $(this).data("id");
    var itemName = GetItemName(sale_id);
    e.preventDefault();
    $("#deleteSaleModal").modal('show');
    $('.delete-ok-btn').on('click', function(){
      ListenAndDoDeletion(sale_id);
    });

  });

   function UpdateSale(sale_id){

    $('.errors-section').html('');
    $('.addSaleBtn').html('Updating sale...');

    var Url = "{{ route('sales.records.update') }}";
    $.ajax({
      data: $('#SaleForm').serialize(),
      url: Url,
      type: "PUT",
      dataType: 'json',
      success: function (data) {

        $('#SaleForm').trigger("reset");
        $('#SalesModal').modal("hide");
        var resp = data.success;
        ShowResponse('.response', resp, 'success');
        ResetTblInfo(data);
        var tbl = $('#sales-table').DataTable();
        tbl.ajax.reload();

      },
      error: function (data) {
        console.log('Error:', data.error);
        ShowResponse('.response', data.error, 'error');
        $('.addSaleBtn').html('Save Changes');
      }
    });

  }

  function onClickSubmitBtn(){
    $('.addSaleBtn').click(function (e) {
      var id = $(".item_id").val();
      e.preventDefault();
      var Errors = validateForm();
      if(Errors.length == 0){
        if(id){
          UpdateSale(id);
        }else{
         console.log("We are doing nothing since there's no sale id detected");
       }
     }else{
      var i;
      var message ="";
      for(i=0; i<Errors.length; i++){
        message += Errors[i] + "<br>";
      }
      $('.errors-section').html(message);
    }
  });
  }

  function validateForm(){
    var date = $('.date').val();
    var errors = [];
    if(date.length < 1){
      var dateErr = "Please enter the date when the item was sold";
      errors.push(dateErr);
    }
    return errors;
  }

  function GetItemName(id){

    var getUrl = '{{ route("getItemName", ":id") }}';
    getUrl = getUrl.replace(':id', id);
    $.ajax({
     type: "GET",
     url: getUrl,
     success: function (data) {
       $('.item-to-delete').html(data.item);
     },
     error: function (data) {
       console.log('Error:', data);    
     }
   });

  }


  function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("sales.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
    $('.delete-ok-btn').html('Deleting...');
    $.ajax({
     type: "DELETE",
     url: deleteUrl,
     success: function (data) {
      var resp = data.success;
      $('.delete-ok-btn').html('Yes');
      $('#deleteSaleModal').modal("hide");
      ShowResponse('.response', resp, 'success');
      ResetTblInfo(data);
      var tbl = $('.sales-table').DataTable();
      tbl.ajax.reload();
    },
    error: function (data) {
     console.log('Error:', data);
     ShowResponse('.response', data.error, 'error');
   }
 });
  }

  function DisableTableFields(bool, boolx){

    $('.item-name').attr('disabled', bool);
    $('.qty').attr('disabled', bool);
    $('.sprice').attr('disabled', bool);
    $('.tcost').attr('disabled', bool);
    $('.discount').attr('disabled', bool);
    $('.amount').attr('disabled', bool);
    $('.customer').attr('disabled', bool);
    $('.cashier').attr('disabled', bool);
    $('.date').attr('disabled', boolx);
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

 function ShowHideBtns(action){

  if(action == 'hide'){
    $('.addSaleBtn').hide();
    $('.clearBtn').hide();
    $('.closeBtn').hide();
  }else if(action == 'show'){
    $('.addSaleBtn').show();
    $('.clearBtn').show();
    $('.closeBtn').show();
  }
}

function ResetTblInfo(response){
 var totl_no, totl_sales, netWorth;
 totl_no = FormatNumber(response.totl_no);
 totl_sales = FormatNumber(response.totl_sales);
 netWorth = FormatNumber(response.net_worth);

 $('.totl_no').html( totl_no);
 $('.totl_sales').html(totl_sales);
 (netWorth > 0)
 ? $('.net_profit').html(netWorth)
 : $('.net_loss').html(netWorth);

}


});

</script>

@endsection
