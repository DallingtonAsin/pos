@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">

    <div class="row nunito-font">
     <div class="pl-4">
       <h5 class="nunito-font"><i class="fa fa-chart-line text-success"></i> 
        <strong>Report / Cashiers Peformance</strong>
      </h5>
    </div>
  </div>
</div>
</div>

<div class="panel-body poppins">
  <div class="table-responsive">
    <table class="table table-bordered" id="TopCashiers-table" >
      <thead>
        <tr>
          <th>No</th>
          <th>Cashier</th>
          <th>Sales </th>
          <th>Percentage (%)</th>
        </tr>
      </thead>
 </table>
</div>
</div>
</div>


<script>
 const ajaxUrl = @json(route('top-cashiers.ajax'));
 const cat = 'top-cashiers';
 const token = "{{ csrf_token() }}";
 var title = "Best Performing Cashiers";
 var table = $('#TopCashiers-table');;
 var columns = [0,1,2,3];

 var dataColumns = [
  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
  {data: 'cashier', name:'cashier'},
  {data: 'totalsales', name:'totalsales'},
  {data: 'percent',name:'percent'},
  ];
  makeDataTable2(table, title, columns, dataColumns);
</script>

@endsection

