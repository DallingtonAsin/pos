@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
    <div class="row">
      <div class="pl-5 pt-3 ">
        <h5 class="nunito-font"><i class="fa fa-chart-line text-success"></i> 
          <strong>Report / Top Customers
         </strong></h5>
       </div> 
     </div>
   </div>
 </div>

 <div class="panel-body poppins">
  <div class="table-responsive">
    <table id="TopCustomers-table" class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Customer</th>
          <th>Worth of items bought</th>
          <th>Percentage (%)</th>
        </tr>
      </thead>
 </table>
</div>
</div>
</div>


<script>

 const ajaxUrl = @json(route('top-customers.ajax'));
 const cat = 'top-customers';
 const token = "{{ csrf_token() }}";
 var title = "Top Customers";
 var table = $('#TopCustomers-table');;
 var columns = [0, 1, 2, 3];

 var dataColumns = [
  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
  {data: 'customer', name:'customer'},
  {data: 'volumeofsales', name:'volumeofsales'},
  {data: 'percent',name:'percent'},
  ];

  makeDataTable2(table, title, columns, dataColumns);

</script>

@endsection

