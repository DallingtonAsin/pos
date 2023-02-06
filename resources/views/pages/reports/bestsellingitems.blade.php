@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
    <div class="row">
      <div class="pl-4 pt-3">
       <h5 class="nunito-font"><i class="fa fa-chart-bar text-success pr-2"></i> 
        <strong>
          Report / Best selling items
        </strong>
      </h5>
    </div>
  </div>
</div>
</div>

<div class="panel-body poppins">
  <div class="table-responsive">
    <table class="table table-bordered" id="BestSellingItems-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Item</th>
          <th>Qty</th>
          <th>Sales</th>
          <th>Sales/Qty ratio</th>
          <th>Percentage</th>
        </tr>
      </thead>
 </table>
</div>
</div>
</div>

<script>

 const ajaxUrl = @json(route('best-selling-items.ajax'));
 const cat = 'best-selling-items';
 const token = "{{ csrf_token() }}";
 var title = "Best Selling Items";
    var table = $('#BestSellingItems-table');
 var columns = [0,1,2,3,4,5];

 var dataColumns = [
  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
  {data: 'item', name:'item'},
  {data: 'quantity', name:'quantity'},
  {data: 'totalsales', name:'totalsales'},
  {data: 'ratio', name:'ratio'},
  {data: 'percent',name:'percent'},
  ];

  makeDataTable2(table, title, columns, dataColumns);

</script>


@endsection

