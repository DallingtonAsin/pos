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
                    <div id="top_items_by_qty_chart" class="chart-card"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                    <div id="top_items_by_revenue_chart" class="chart-card"></div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                    <div id="top_items_by_profit_chart" class="chart-card"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                    <div id="overview_chart" class="chart-card"></div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {

        let topItemsByQty = {!! json_encode($data['topItemsByQty']) !!};
        let topItemsByRevenue = {!! json_encode($data['topItemsByRevenue']) !!};
        let topItemsByProfit = {!! json_encode($data['topItemsByProfit']) !!};


        // let paid_orders = {!! json_encode($customers) !!};
        // let cancelled_orders = {!! json_encode($customers) !!};
        // let completed_orders = {!! json_encode($customers) !!};


        // console.log('Paid orders', paid_orders);
        // console.log('Cancelled orders', cancelled_orders);
        // console.log('Completed orders', completed_orders);



     
       eBarGraph('top_items_by_qty_chart', 'Top Sold Items By Quantity', topItemsByQty.quantity, topItemsByQty.items, 'items', '#0dcaf0');
       ePieChart('top_items_by_revenue_chart', 'Top Sold Items By Revenue', topItemsByRevenue);
       eLineGraph('top_items_by_profit_chart', 'Top Sold Items by Profit', topItemsByProfit.profit, topItemsByProfit.items, 'items', '#198754');

     


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