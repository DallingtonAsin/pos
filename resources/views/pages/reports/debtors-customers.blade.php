@extends('layouts.master')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
   <div class="panel-title nunito-font">

    <div class="row">
      <h5 class="nunito-font pl-3"><i class="fa fa-chart-line text-success"></i> 
       <strong class="pl-1">
         Report <i class="zmdi zmdi-play "></i> Customer debtors
       </strong></h5>
     </div>
   </div>
 </div>
 
 <div class="panel-body">
  <div class="table-responsive poppins">
    <table class="table table-bordered" id="CustomerDebtors-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Customer</th>
          <th>Contact</th>
          <th>Debts</th>
          <th>Percentage (%)</th>
        </tr>
      </thead>
   </table>
 </div>
</div>
</div>

<script>

 const ajaxUrl = @json(route('debtors-customers.ajax'));
 const cat = 'debtors-customers';
 const token = "{{ csrf_token() }}";
 var title = "Customer Debtors";
 var table = $('#CustomerDebtors-table');
 var columns = [0, 1 ,2, 3, 4];

 var dataColumns = [
  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
  {data: 'name', name:'name'},
  {data: 'contact', name:'contact'},
  {data: 'debts', name:'debts'},
  {data: 'percent',name:'percent'},
  ];

  makeDataTable2(table, title, columns, dataColumns);

</script>


@endsection
