@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
    <div class="panel-title">
      <span class="pl-0 mt-4 response"></span>
      <div class="row nunito-font">
        <div class="col-lg-3 text-dark">
         <h6>
          <i class="fa fa-home text-success"> /</i>
          <strong>Stock</strong>
          <span class="badge nunito-font  totl-stock">
            @isset($number_of_stockItems)
            {{ number_format($number_of_stockItems) }}
            @endisset
          </span>
        </h6>
      </div>

      @can('isAdmin')
      <div class="col-lg-3">
        <span><h5>
          Current stock value:
          <span class="text-success text-center">shs.
            <strong class="stock-value">
              @isset($stock_value)
              {{ number_format($stock_value) }}
              @endisset
            </strong>
          </span>
        </h5>
      </span>
    </div>
    @endcan
    <div class="col-lg-2">
     <h5>
      <a class="add-link text-info text-decoration-none"
      href="javascript:void(0)"
      id="createNewStock"><strong>Add Stock</strong> </a>
    </h5>
  </div>

  <div class="col-lg-2">
   <h5><a href=""  class="add-link text-info text-decoration-none" data-toggle="modal" data-target="#importStock"><strong>Import stock</strong></a></h5>
 </div>

 @can('isAdmin')
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
      id="removeAllStockItems">
      <i class="fa fa-trash-alt text-danger"></i> Delete all stock</a>
    </li>
  </ul>
</div>
</div>
@endcan

</div>
</div>
</div>

<div class="panel-body">
  <div class="row">
    <div class="col-lg-10 text-center nunito-font">
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


  <div class="table table-responsive custom-family">
    <table class="table stock-table table-bordered table-hover" id="stock-table">
      <thead>
        <tr>
          @can('isAdmin')
          <th></th>
          @endcan
          @can('isCashier')
          <th>No</th>
          @endcan
          <th>Item</th>
          <th>Item Code</th>
          <th>Qty</th>
          @can('isAdmin')
          <th>Buying Price</th>
          @endcan
          <th>Retail Price</th>
          <th>Wholesale Price</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div> 


  <!--Add new Stock -->
  <div class="modal fade nunito-font" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <form name="StockForm" id="StockForm">
         @csrf
         <div class="modal-header text-center">
          <h5 class="modal-title w-100 font-weight-bold custom-family" id="modalHeading">
          Add new stock item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <span>Item ID</span>
            <input type="hidden" class="stockId" name="id">
            <input type="text" class="form-control bg-white item_code" name="item_code"
            placeholder="Enter item ID">
          </div>

          <div class="form-group">
            <span>Item</span>
            <input type="text" class="form-control bg-white item-name" name="item"
            placeholder="Enter item" Required autofocus>

          </div>

          <div class="form-group">
            <span>Category</span>
            <select class="form-control bg-white category" name="category" Required autofocus id="category" >
              <option value="" selected="true">choose category</option>
              @foreach($categories as $category)
              <option value="{{ $category->item_category }}"> {{ $category->item_category }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <span>Supplier</span>
            <select class="form-control bg-white" id="supplier" name="supplier" Required autofocus>
              <option value="" selected="true">choose supplier</option>
              @foreach($suppliers as $supplier)
              <option value="{{ $supplier->name }}"> {{ $supplier->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <span>Quantity</span>
            <input type="text" class="form-control bg-white quantity"  id="qty" name="quantity" placeholder="Enter Quantity" Required autofocus>
          </div>

          <div class="form-group">
            <span>Threshold Quantity</span>
            <input type="text" class="form-control bg-white thresholdQty"  id="thresholdQty" name="thresholdQty" placeholder="Enter threshold quantity">
          </div>

          <div class="form-group">
            <span>Expiry Date</span>
            <input type="date" class="form-control bg-white expiry_date" name="expiry_date" placeholder="Enter who bought it">
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-lg-4">
                <span>Buying Price</span>
                <input type="text" class="form-control bg-white original_price" name="original_price" placeholder="Enter original price" Required autofocus>
              </div>

              <div class="col-lg-4 form-group">
                <span>Retail Price</span>
                <input type="text" class="form-control bg-white selling_price" name="selling_price" placeholder="Enter selling price" Required autofocus>
              </div>

              <div class="col-lg-4 form-group">
                <span>Wholesale Price</span>
                <input type="text" class="form-control bg-white  wholesale_price" name="wholesale_price" placeholder="Enter wholesale price" Required autofocus>
              </div>

            </div>
          </div>


          <div class="form-group">
            <button type="submit" class="btn btn-primary addStockBtn"  name="AddItemBtn">Save</button>
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

<!--Import Stock -->
<div class="modal fade nunito-font" id="importStock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form action="{{ Route('stock.import') }}" method="post"
      enctype="multipart/form-data" name="inportStockForm" >
      @csrf

      <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold custom-family">
        Import an excel file of stock items</h5>
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

<!--Modal DeleteStock -->
<div class="modal fade" id="deleteStockModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title w-100 font-weight-bold">Delete Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="form-group">
          <div class="text-center">
           <label class="text-danger">Are you sure you want to delete item
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
</div>
<!-- end of modal DeleteStock-->
</div>
</div>

<script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('vendors/notify/notify.js') }}"></script>
<script>
  const ajaxUrl =   @json(route('get-stock'));
  const deletedSeletectedUrl = @json(route('selected-stock.remove'));
  const cat = 'stock';
  const token = "{{ csrf_token() }}";
</script>

@can('isAdmin')
<script>
    //code that displays results of the table index()
    var table = $('#stock-table');
    var title = "List of stock items in the system";
    var columns = [1,2,3,4,5];
    var dataColumns = [
    {data: 'checkbox', name:'checkbox'},
      // {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
         // {data: 'id', name:'id'},
        //  {data: 'item_code', name:'item_code'},
        {data: 'item', name:'item'},
        {data: 'item_code', name:'item_code'},

        {data: 'quantity', name:'quantity'},
        //  {data: 'threshold_qty', name:'threshold_qty'},
        {data: 'buying_price', name:'buying_price'},
        {data: 'selling_price', name:'selling_price'},
        {data: 'wholesale_price', name:'wholesale_price'},
        //  {data: 'supplier', name:'supplier'},
        {data: 'action', name:'action',orderable: false,searchable: false},
        ];
        makeDataTable(table, title, columns, dataColumns);
      </script>
      @endcan

      @can('isCashier')
      <script>
    //code that displays results of the table index()
    var table = $('#stock-table');
    var title = "List of stock items in the system";
    var columns = [1,2,3,4,5];
    var dataColumns = [
        //  {data: 'id', name:'id'},
        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        {data: 'item', name:'item'},
        {data: 'item_code', name:'item_code'},
        {data: 'quantity', name:'quantity'},
        {data: 'selling_price', name:'selling_price'},
        {data: 'wholesale_price', name:'wholesale_price'},
        {data: 'action', name:'action',orderable: false,searchable: false},
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
         Numberize(".quantity");
         Numberize(".thresholdQty");
         Numberize(".original_price");
         Numberize(".selling_price");
         Numberize(".wholesale_price");

         function Numberize(i){
          $(document).on("keyup", i , function(){
            if(this.value.length > 0){
              var n = parseInt(this.value.replace(/\D/g,''), 10);
              $(this).val(n.toLocaleString());
            }
          });
        }

        $.fn.dataTable.ext.errMode = 'none';
        $('#stock-table').on( 'error.dt', function ( e, settings, techNote, message ) {
          console.log( 'An error has been reported by DataTables: ', message );
        }).DataTable();

        onClickSubmitBtn();

        $('#createNewStock').click(function (e) {
         e.preventDefault();
         NullifyFields();
         ShowHideBtns('show');
         $('.addStockBtn').text("Record stock");
         $('#StockForm').trigger("reset");
         $('#modalHeading').html("Record new stock");
         DisableFormFields(false);
         $('#addStockModal').modal('show');

       });

//modal used to edit stock details [each row of the tbl]
$('body').on('click', '#edit-stock', function (event) {
  var stock_id = $(this).data('id');
  event.preventDefault();

  ShowHideBtns('show');
  $('.addStockBtn').text("Edit stock");
  $('#addStockModal').modal('show');
  var Url = "{{ route('stock.show', ':id') }}";
  Url = Url.replace(':id', stock_id);
  $.ajax({

    url: Url,
    type: "GET",
    dataType: 'json',
    success: function (data) {

      $('#modalHeading').html("Edit details of stock item " + data.item + "");
      $('.stockId').val(data.id);
      $('.item_code').val(data.item_code);
      $('.item-name').val(data.item);
      if(data.category){
        $('#category').val(data.category);
      }else{
        $('#category').val("choose category");
      }
      $('#supplier').val(data.supplier);
      $('.quantity').val(data.quantity);
      $('.threshold_qty').val(data.threshold_qty);
      $('.expiry_date').val(data.expiry_date);
      $('.original_price').val(data.buying_price);
      $('.selling_price').val(data.selling_price);
      $('.wholesale_price').val(data.wholesale_price);
      DisableFormFields(false);
    },
    error: function (data) {
      console.log('Error:', data.error);
      ShowResponse('.response', data.error, 'error');
    }
  });

});




function UpdateStock(stock_id){

  $('.errors-section').html('');
  $('.addStockBtn').html('Updating item...');

  var Url = "{{ route('stock.update', ':id') }}";
  Url = Url.replace(':id', stock_id);
  $.ajax({
    data: $('#StockForm').serialize(),
    url: Url,
    type: "PUT",
    dataType: 'json',
    success: function (data) {

      $('#StockForm').trigger("reset");
      $('#addStockModal').modal("hide");
      var resp = data.success;
      ShowResponse('.response', resp, 'success');
      ResetTblInfo(data);
      var tbl = $('#stock-table').DataTable();
      tbl.ajax.reload();

    },
    error: function (data) {
      console.log('Error:', data.error);
      ShowResponse('.response', data.error, 'error');
      $('.addStockBtn').html('Save Changes');
    }
  });

}

function recordStock(){

  $('.errors-section').html('');
  $('.addStockBtn').html('Sending data..');

  $.ajax({
    data: $('#StockForm').serialize(),
    url: "{{ route('stock.store') }}",
    type: "POST",
    dataType: 'json',
    success: function (data) {

      $('#StockForm').trigger("reset");
      $('#addStockModal').modal("hide");
      var resp = data.success;
      ShowResponse('.response', resp, 'success');
      ResetTblInfo(data);
      var tbl = $('#stock-table').DataTable();
      tbl.ajax.reload();

    },
    error: function (data) {
      console.log('Error:', data.error);
      ShowResponse('.response', data.error, 'error');
      $('.addStockBtn').html('Save Changes');
    }
  });

}


   //View Modal used to view each row [stock details]
   $('body').on('click', '#view-stock', function (event) {
    var stock_id = $(this).data('id');
    event.preventDefault();
    ShowHideBtns('hide');
    $.get("{{ route('stock.index') }}" +'/' + stock_id +'', function (data) {
      var bprice = data.buying_price;
      var sprice = data.selling_price;
      var wprice = data.wholesale_price;
      $('#modalHeading').html("Details of stock " + data.item + "");
      $('#addStockModal').modal('show');
      $('.stockId').val(stock_id);
      $('.item_code').val(data.item_code);
      $('.item-name').val(data.item);
      $('.category').val(data.category);
      $('#supplier').val(data.supplier);
      $('.quantity').val(data.quantity);
      $('.thresholdQty').val(data.threshold_qty)
      $('.expiry_date').val(data.expiry_date);
      $('.original_price').val(bprice);
      $('.selling_price').val(sprice);
      $('.wholesale_price').val(wprice);
      DisableFormFields(true);

    });
  });


   function onClickSubmitBtn(){
    $('.addStockBtn').click(function (e) {
      var id = $(".stockId").val();
      e.preventDefault();
      var Errors = validateForm();
      if(Errors.length == 0){
        if(id){
          UpdateStock(id);

        }else{
          recordStock();
        }

      }else
      {
        var i;
        var message ="";
        for(i=0; i<Errors.length; i++){
          message += Errors[i] + "<br>";
        }
            //ShowResponse('.errors-section', resp, 'error');
            $('.errors-section').html(message);

          }

        });
  }




   //this pops up confirm delete modal
   $('body').on('click', '#delete-stock', function (e) {
    var stock_id = $(this).data("id");
    e.preventDefault();
    $("#deleteStockModal").modal('show');
    $('.delete-ok-btn').on('click', function(){
     ListenAndDoDeletion(stock_id);
   });

  });


   function ListenAndDoDeletion(id){
    var deleteUrl = '{{ route("stock.destroy", ":id") }}';
    deleteUrl = deleteUrl.replace(':id', id);
    $('.delete-ok-btn').html('Deleting...');
    $.ajax({
     type: "DELETE",
     url: deleteUrl,
     success: function (data) {
      var resp = data.success;
      $('.delete-ok-btn').html('Yes');
      $('#deleteStockModal').modal("hide");
      ShowResponse('.response', resp, 'success');
      ResetTblInfo(data);
      var tbl = $('#stock-table').DataTable();
      tbl.ajax.reload();
    },
    error: function (data) {
     console.log('Error:', data);
     ShowResponse('.response', data.error, 'error');
   }
 });
  }

  function NullifyFields(){
    $('.stockId').val('');
    $('.item_code').val('');
    $('.item-name').val('');
    $('.category').val('');
    $('#supplier').val('');
    $('.quantity').val('');
    $('.expiry_date').val('');
    $('.original_price').val('');
    $('.selling_price').val('');
    $('.wholesale_price').val('');
  }



  function DisableFormFields(bool){

    $('.stockId').attr('disabled', bool);
    $('.item_code').attr('disabled', bool);
    $('.item-name').attr('disabled', bool);
    $('.category').attr('disabled', bool);
    $('#supplier').attr('disabled', bool);
    $('.quantity').attr('disabled', bool);
    $('.expiry_date').attr('disabled', bool);
    $('.original_price').attr('disabled', bool);
    $('.selling_price').attr('disabled', bool);
    $('.wholesale_price').attr('disabled', bool);
  }

  function ShowHideBtns(action){

    if(action == 'hide'){
      $('.addStockBtn').hide();
      $('.clearBtn').hide();
      $('.closeBtn').hide();
    }else if(action == 'show'){
      $('.addStockBtn').show();
      $('.clearBtn').show();
      $('.closeBtn').show();
    }
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
   var totl_stock , stockValue;
   totl_stock = FormatNumber(response.totl_stock);
   stockValue = FormatNumber(response.stock_value);

   $('.totl-stock').html(totl_stock);
   $('.stock-value').html(stockValue);
 }

 function validateForm()
 {
  var item = $('.item-name').val();
  var qty = $('#qty').val();
  var bprice = $('.original_price').val();
  var sprice = $('.selling_price').val();

  var errors = [];
  if(item.length < 1){
    var itemNameErr = "Please enter the name of stock item";
    errors.push(itemNameErr);
  }
  if(qty.length < 1){
    var qtyErr = "Please enter valid quantity of stock";
    errors.push(qtyErr);
  }
  if(bprice == ""){
   var bPriceErr = "Please enter valid buying price of an item";
   errors.push(bPriceErr);
 }

 if(sprice == ""){
   var sPriceErr = "Please enter valid selling price of an item";
   errors.push(sPriceErr);
 }

 return errors;

}

$("#removeAllStockItems").bind("click", function(){
 RemoveAllStockItems();
});

function RemoveAllStockItems(){
 $.confirm({
   boxWidth: '30%',
   icon: 'fa fa-warning',
   theme:'light',
   closeIcon: true,
   draggable:true,
   closeIconClass: 'fa fa-close text-danger',
   title: 'Delete all stock',
   content:'Are you sure you want to remove all stock items',
   buttons:{
     confirm:function(){
       var self = this;
       return $.ajax({
         data: {
           "_token": "{{ csrf_token() }}",
         },
         url: '{{ Route("stock.truncate") }}',
         type: 'POST',
         // dataType: 'json',
       }).done(function (data) {

         $.alert({
           title: 'Message',
           content: data.success,
         });
         $(".totl-stock").text(data.totl_stock);
         $(".stock-value").text(data.stock_value);
         var tbl = $('#stock-table').DataTable();
         tbl.ajax.reload();


       }).fail(function(data){
         $.alert({
           title: 'Response',
           content:"Stock not deleted:"+data.fail,
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