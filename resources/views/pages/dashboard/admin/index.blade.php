<div class="az-content-body dashboard-body">

    <div class="row">
        <a href="{{ route('sales.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small primary coloured-icon"><i class="icon fa fa-shopping-cart fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Sales</h4>
                    <p>
                        <b>
                            @isset($data)
                                {{ number_format($data['total_sales']) }}
                            @endisset
                        </b>
                    </p>
                </div>
            </div>
        </a>


        <a href="{{ route('stock.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-database fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Stock</h4>
                    <p><b>
                            @isset($data)
                                {{ number_format($data['num_of_stockItems']) }}
                            @endisset
                        </b></p>
                </div>
            </div>
        </a>


        <a href="{{ route('expenses.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-minus-circle fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Expenses</h4>
                    <p><b>
                            @isset($data)
                                {{ number_format($data['total_expenses']) }}
                            @endisset
                        </b></p>
                </div>
            </div>
        </a>

        <a href="{{ route('damaged-stock-items.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small danger coloured-icon"><i class="icon fa fa-cube fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Damaged stock</h4>
                    <p><b>
                            @isset($data)
                                {{ number_format($data['total_damages']) }}
                            @endisset
                        </b></p>
                </div>
            </div>
        </a>
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                {{-- <div class="embed-responsive"> --}}
                    <div id="top_items_by_qty_chart" class="chart-card"></div>
                {{-- </div> --}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                {{-- <div class="embed-responsive"> --}}
                    <div id="paid_order_chart" class="chart-card"></div>
                {{-- </div> --}}
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                {{-- <div class="embed-responsive"> --}}
                    <div id="cancelled_order_chart" class="chart-card"></div>
                {{-- </div> --}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                {{-- <div class="embed-responsive"> --}}
                    <div id="overview_chart" class="chart-card"></div>
                {{-- </div> --}}
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {

        let topItems = {!! json_encode($data['topItems']) !!};
        // let paid_orders = {!! json_encode($customers) !!};
        // let cancelled_orders = {!! json_encode($customers) !!};
        // let completed_orders = {!! json_encode($customers) !!};

        console.log('Top items', topItems);
        console.log('Top items', topItems.items);
        console.log('Top items', topItems.quantity);

        // console.log('Paid orders', paid_orders);
        // console.log('Cancelled orders', cancelled_orders);
        // console.log('Completed orders', completed_orders);



     
       eBarGraph('top_items_by_qty_chart', 'Top sold items by quantity', topItems.quantity, topItems.items, 'items', '#0dcaf0');
        

        // if (paid_orders != undefined || paid_orders.length > 0) {
        //     ePieChart('paid_order_chart', 'Monthly Paid Kitchen Orders', paid_orders);
        // }

        // if (cancelled_orders != undefined || cancelled_orders.length > 0) {
        //     eLineGraph('cancelled_order_chart', 'Monthly Cancelled Kitchen Orders', cancelled_orders.orders,
        //         cancelled_orders.months, 'orders', '#198754');
        // }


        // if ((monthly_orders != undefined || monthly_orders.length > 0) &&
        //     (completed_orders != undefined || completed_orders.length > 0) &&
        //     (cancelled_orders != undefined || cancelled_orders.length > 0)) {

        //     let metricsData = [];
        //     let metrics = ["Total orders", "Paid orders", "Cancelled orders"];
        //     let title = "Total Orders vs Paid Orders vs Cancelled Orders";

        //     metricsData[0] = monthly_orders.orders;
        //     metricsData[1] = completed_orders.orders;
        //     metricsData[2] = cancelled_orders.orders;
        //     get2BarsAndLineGraphOptions('overview_chart', title, metrics, monthly_orders.months, metricsData);
        // } else {
        //     console.log("monthly orders", monthly_orders.length);
        //     console.log("completed orders", completed_orders.length);
        //     console.log("cancelled orders", cancelled_orders.length);

        // }

    });
</script>