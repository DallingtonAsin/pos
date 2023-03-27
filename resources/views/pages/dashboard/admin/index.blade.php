<div class="az-content-body dashboard-body">

    <div class="row">
        <a href="{{ route('sales.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small primary coloured-icon"><i class="icon fa fa-shopping-cart fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Sales</h4>
                    <p>
                        @isset($data)
                            <strong>{{ number_format($data['total_sales']) }}</strong>
                        @endisset
                    </p>
                </div>
            </div>
        </a>


        <a href="{{ route('stock.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-database fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Stock</h4>
                    <p>
                        @isset($data)
                            <strong>{{ number_format($data['num_of_stockItems']) }}</strong>
                        @endisset
                    </p>
                </div>
            </div>
        </a>


        <a href="{{ route('expenses.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-minus-circle fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Expenses</h4>
                    <p>
                        @isset($data)
                            <strong>{{ number_format($data['total_expenses']) }}</strong>
                        @endisset
                    </p>
                </div>
            </div>
        </a>

        <a href="{{ route('damaged-stock-items.index') }}" class="col-md-6 col-lg-3 text-decoration-none">
            <div class="widget-small danger coloured-icon"><i class="icon fa fa-cube fa-3x"></i>
                <div class="info">
                    <h4 class="widget-title">Damages</h4>
                    <p>
                        @isset($data)
                            <strong>{{ number_format($data['total_damages']) }}</strong>
                        @endisset
                    </p>
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
                <div id="sales_by_cashier_chart" class="chart-card"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                <div id="top_items_by_profit_chart" class="chart-card"></div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {

        let topItemsByQty = {!! json_encode($data['topItemsByQty']) !!}
        let topItemsByRevenue = {!! json_encode($data['topItemsByRevenue']) !!}
        let topItemsByProfit = {!! json_encode($data['topItemsByProfit']) !!}
        let salesByCashier = {!! json_encode($data['salesByCashier']) !!}

        ePieChart('sales_by_cashier_chart', 'Sales By Cashier', salesByCashier)
        ePieChart('top_items_by_revenue_chart', 'Top Sold Items By Revenue', topItemsByRevenue)
        eBarGraph('top_items_by_qty_chart', 'Top Sold Items By Qty', topItemsByQty.quantity, topItemsByQty.items, 'items', '#0dcaf0')
        eLineGraph('top_items_by_profit_chart', 'Top Sold Items by Profit', topItemsByProfit.profit, topItemsByProfit.items, 'items', '#198754')
    });
</script>