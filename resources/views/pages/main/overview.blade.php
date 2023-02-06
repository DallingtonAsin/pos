@extends('layouts.master')

  @section('content')


  {{-- <div class="card-table"> --}}
    <div class="card card-dashboard-table-six">

     <div class="card-title nunito-font mg-b-0">
       <i class="fa fa-home text-success "></i>
       Dashboard <small class="fa fa-angle-double-right pt-2"></small> Overview
     </div>

     <div class="card-body">

      <div class="az-content-body">
        <div class="row row-sm">

          <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card card-body card-dashboard-fifteen">
              <h1>
                @isset($data)
                {{ number_format($data['total_sales']) }}
                @endisset
              </h1>
              <label class="tx-purple">Sales Made</label>
              <span>The total number of sales that have been made so far.</span>
              <div class="chart-wrapper">
                <div id="flotChart1" class="flot-chart"></div>
              </div><!-- chart-wrapper -->
            </div><!-- card -->
          </div><!-- col -->

          <div class="col-sm-6 col-lg-4 col-xl-3 mg-t-20 mg-sm-t-0">
            <div class="card card-body card-dashboard-fifteen">
              <h1>
                @isset($data)
                {{ number_format($data['num_of_stockItems']) }}
                @endisset
              </h1>
              <label class="tx-primary">Stock Available</label>
              <span>The total number of items currently available in stock.</span>
              <div class="chart-wrapper">
                <div id="flotChart2" class="flot-chart"></div>
              </div><!-- chart-wrapper -->
            </div><!-- card -->
          </div><!-- col -->

          <div class="col-sm-6 col-lg-4 col-xl-3 mg-t-20 mg-sm-t-20 mg-lg-t-0">
            <div class="card card-body card-dashboard-fifteen">
              <h1>
                @isset($data)
                {{ number_format($data['total_expenses']) }}
                @endisset
                <span></span>
              </h1>
              <label class="tx-teal">Expenses Recorded</label>
              <span>The total number of recorded expenses.</span>
              <div class="chart-wrapper">
                <div id="flotChart4" class="flot-chart"></div>
              </div><!-- chart-wrapper -->
            </div><!-- card -->
          </div><!-- col -->

          <div class="col-sm-6 col-lg-4 col-xl-3 mg-t-20 mg-sm-t-20 mg-lg-t-0">
            <div class="card card-body card-dashboard-fifteen">
              <h1>
                @isset($data)
                {{ number_format($data['total_customers']) }}
                @endisset
                <span></span>
              </h1>
              <label class="tx-purple">Customers</label>
              <span>The total number of recorded customers.</span>
              <div class="chart-wrapper">
                <div id="flotChart3" class="flot-chart"></div>
              </div><!-- chart-wrapper -->
            </div><!-- card -->
          </div><!-- col -->


          <div class="col-xl-6 mg-t-15 mg-t-20">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title tx-14 mg-b-5">Customer Satisfaction</h6>
                <p class="tx-gray-600 mg-b-0">Measures the quality or your support team’s efforts. It is important to monitor your customer satisfaction status, as the opinion... <a href="">Learn more</a></p>
              </div><!-- card-header -->
              <div class="card-body row pd-25">
                <div class="col-sm-8 col-md-7">
                  <div id="flotPie" class="wd-100p ht-200"></div>
                </div><!-- col -->
                <div class="col-sm-4 col-md-5 mg-t-30 mg-sm-t-0">
                  <ul class="list-unstyled">
                    <li class="d-flex align-items-center"><span class="d-inline-block wd-10 ht-10 bg-purple mg-r-10"></span> Very Satisfied (26%)</li>
                    <li class="d-flex align-items-center mg-t-5"><span class="d-inline-block wd-10 ht-10 bg-primary mg-r-10"></span> Satisfied (39%)</li>
                    <li class="d-flex align-items-center mg-t-5"><span class="d-inline-block wd-10 ht-10 bg-teal mg-r-10"></span> Not Satisfied (20%)</li>
                    <li class="d-flex align-items-center mg-t-5"><span class="d-inline-block wd-10 ht-10 bg-gray-500 mg-r-10"></span> Satisfied (15%)</li>
                  </ul>
                </div><!-- col -->
              </div><!-- card-body -->
            </div><!-- card -->
          </div><!-- col -->



          <div class="col-gl-5 col-xl-6 mg-t-20">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title tx-14 mg-b-5 nunito-font">Cashiers</h6>
                <p class="tx-gray-600 mg-b-0">Measure the performance your support persons [cashiers] spend
                 attending to their work / customer. It gives your individual insight into
                 how best they invest in their work... <a href="{{ route('top-cashiers') }}">Learn More</a></p>
               </div><!-- card-header -->
               <div class="table-responsive mg-t-15">
                <table class="table table-responsive table-talk-time">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Cashier</th>
                      <th>(%)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @isset($data)
                    @php
                    $count = 1;
                    @endphp

                    @foreach($data['top_cashiers'] as $element)

                    <tr>
                      <td>{{ $count++ }}</td>
                      <td>{{ $element->cashier }}</td>
                      <td>{{ $element->percent }}</td>
                    </tr>
                    @endforeach

                    @endisset

                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div><!-- card -->
          </div><!-- col -->


          <div class="col-md-12 col-lg-12 col-xl-12 mg-t-20">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title tx-14 mg-b-5">Customer Complaints Comparison</h6>
                <p class="mg-b-0">Monitor the total number of complaints that are resolved and unresolved.</p>
              </div><!-- card-header -->
              <div class="card-body">
                <div class="dashboard-five-stacked-chart"><canvas id="chartStacked1"></canvas></div>
              </div><!-- card-body -->
            </div><!-- card -->
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- az-content-body -->

    </div>
    </div>

    @endsection



