@extends('layouts.master')

@section('content')
     {{-- <div class="card-table"> --}}
          <div class="card card-dashboard-table-six">
            <h6 class="card-title">
              <i class="glyphicon glyphicon-th text-success"></i> 
                 <span class="pl-3">Help Desk</span>

               </h6>


            <div class="card-body ">

              <form class="form-group" >

             <div class="d-flex justify-content-center">
              <div class="form-group">
                <p><i class="text-info">1. User login page</i><br>
                  Here the user enters credentials i.e username and password
                  to login to able able to access the system.
                </p>
                <img src='{{ asset('data/documentation/login.PNG') }}'
                 class='manuals'/>
                </div>

              <div class="form-group">
                <p><i class="text-info">2. Dashboard</i><br>
                The dashboard page welcomes the system user to perform tasks which can
                be selected from the left dashboard.
                </p>
              <img src='{{ asset('data/documentation/manager/dashboard.png') }}'
              class='manuals'/>
              </div>

            </div>


            <div class="d-flex justify-content-center">
              <div class="form-group">
                <p><i class="text-info">3. Overview</i><br>
                  This page shows statistics about business transactions like numbers of available
                  sales,stock,expenses,customers etc.
                </p>
                <img src='{{ asset('data/documentation/manager/dashboard.png') }}'
                 class='manuals'/>
                </div>

              <div class="form-group">
                <p><i class="text-info">4. Stock</i><br>
                The stock page allows the manager to add stock by adding item by item or importing
                an excel file containing stock.The manager can also edit & delete stock items.
                Manager can also export current stock items in an excel file.
                </p>
              <img src='{{ asset('data/documentation/manager/stock.png') }}'
              class='manuals'/>
              </div>
            </div>



            <div class="d-flex justify-content-center">

              <div class="form-group">
                <p><i class="text-info">5. Suppliers</i><br>
                The suppliers page allows the manager to register  a supplier or importing
                an excel file containing suppliers.The manager can also edit & delete suppliers.Manager
                can also export list of suppliers in an excel file.
                </p>
              <img src='{{ asset('data/documentation/manager/stock.png') }}'
              class='manuals'/>
            </div>

              <div class="form-group">
                <p><i class="text-info">6. Cashiers</i><br>
                  The cashiers page allows the manager to register a cashier or importing
                  an excel file containing cashiers.The manager can also edit & delete cashiers.Manager
                  can also export list of cashiers in an excel file.
                </p>
                <img src='{{ asset('data/documentation/manager/cashiers.png') }}'
                 class='manuals'/>
                </div>

            </div>

              </form>
            </div>
          </div>
        {{-- </div> --}}



@endsection

