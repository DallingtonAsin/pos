@extends('layouts.master')

@section('content')


<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
    <div class="row">
      <div class="pl-3">
        <h5 class="nunito-font pl-3">
          <i class="fa fa-chart-line text-success"></i> 
          <strong>Report / Low Running Stock</strong>
        </h5>
      </div>
    </div>
  </div>
</div>

<div class="panel-body poppins">
  <div class="table-responsive">
    <table class="table table-bordered" id="LowStock-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Item</th>
          <th>Category</th>
          <th>Qty</th>
          <th>Min.Qty</th>
          <th>Buying Price</th>
          <th>Selling Price</th>
          <th>Supplier</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
</div>

<script>
 $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

 const ajaxUrl = @json(route('low-stock.ajax'));
 const cat = 'low-running-stock';
 const token = "{{ csrf_token() }}";
 var table = $('#LowStock-table');
 var title = "Items running out of stock";
 var columns = [0,1,2,3,4,5,6,7];

 var dataColumns = [
  {data: 'id', name:'id'},
  {data: 'item_code', name:'item_code'},
  {data: 'item', name:'item'},
  {data: 'quantity', name:'quantity'},
  {data: 'threshold_qty',name:'threshold_qty'},
  {data: 'buying_price', name:'buying_price'},
  {data: 'selling_price', name:'selling_price'},
  {data: 'supplier', name:'supplier'},
  ];

  makeDataTable2(table, title, columns, dataColumns);

</script>

@endsection

