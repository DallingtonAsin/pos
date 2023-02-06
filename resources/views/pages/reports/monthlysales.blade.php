@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
      <strong>
        <i class="fa fa-chart-line text-success pr-2"></i> 
        Monthly statistics as of  {{ date('d-M-Y H:i A')}}
      </strong>
  </div>
</div>

<div class="panel-body poppins">
  <div class="table table-responsive">
    <table class="table table-bordered" id="MonthlySales-table">
      <thead>
        <tr>
          <th>No.</th>
          <!-- <th>Period</th> -->
          <th>Month</th>
          <th>Year</th>
          <th>Purchases</th>
          <th>Sales</th>
          <th>% of total sales</th>
          <th>Profits</th>
        </tr>
      </thead>
  </table>
</div>
</div>
</div>


<script>

 const ajaxUrl = @json(route('monthly-sales.ajax'));
 const cat = 'monthly-sales';
 const token = "{{ csrf_token() }}";
 var title = "Monthly Sales";
 var table = $('#MonthlySales-table');
 var columns = [0,1,2,3,4];

 var dataColumns = [
  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
  // {data: 'period', name:'period'},
  {data: 'month', name:'month'},
  {data: 'year', name:'year'},
  {data: 'TotalPurchases', name:'TotalPurchases'},
  {data: 'sales', name:'sales'},
  {data: 'percent',name:'percent'},
  {data: 'profits', name:'profits'},
  ];

  makeDataTable2(table, title, columns, dataColumns);

</script>

@endsection

