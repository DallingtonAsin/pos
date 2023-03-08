@extends('layouts.master')


@section('content')
    <div class="panel panel-success" id="panel">
        <div class="panel-heading cartPanelHeader" id="panel-heading">
            <div class="panel-title nunito-font">

                <div class="row">
                    <span class='response'></span>
                    <div class="col-lg-4">
                        <i class="typcn typcn-shopping-cart"></i> cart
                        <span class="badge">
                            <span id="num">0</span>
                        </span>
                    </div>

                    <div class="col-lg-4 mt-2 amount">
                        <strong>Amount: </strong>
                        <strong class="text-danger amountToPay" id="amountToPay">0</strong>
                        <input type="hidden" value="@isset($item_total) {{ $item_total }} @endisset"
                            class="payment">
                    </div>

                </div>

            </div>
        </div>

        <header class="mt-3 mx-4">
            <form id="CartForm" class="form">
                @csrf

                <div class="row">

                    <div class="form-group col-md-2">
                        <label>Tendered Amount</label>
                        <input type="text" class="form-control tendered" id='tendered' placeholder="Tendered amount">
                    </div>

                    <div class="form-group col-md-2">
                        <label>Balance</label>
                        <input type="text" class="form-control  balance" readonly placeholder="Customer balance">
                    </div>

                    <div class="form-group col-md-2">
                        <label>Extra Money Paid</label>
                        <input type="text" class="form-control  extra_money" value="" id="extra_money"
                            placeholder="0">
                    </div>

                    <div class="form-group col-md-2">
                        <label>Cashier</label>
                        <input type="text" class="form-control  cashier" id="cashier" name="cashier"
                            value="{{ Auth::user()->name }}" placeholder="WorkedOn By" readonly>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Customer</label>
                        <select name="customer" id="customer" class="form-control customer">
                            <option value="">Select customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2 mt-4 pt-3">
                        <a href="javascript:void(0)" id="emptyCart" class="btn btn-sm btn-danger">
                            <i class="fa f-10 fa-times-circle pr-2"></i>Empty cart
                        </a>

                    </div>


                </div>

                <div class="row nunito-font">
                    <div class="form-group col">
                        <label>Barcode</label>

                        <input type="text" id="barcode" name="barcode" class="form-control barcode"
                            placeholder="Barcode" autocomplete="off" spellcheck="false" required autofocus>

                    </div>

                    <div class="form-group col">
                        <label>Item name</label>
                        <input type="text" id="item-name" name="item-name" class="form-control item-name"
                            placeholder="Item name" autocomplete="off" spellcheck="false" required>
                    </div>
                    <div class="form-group col">
                        <label>Quantity</label>
                        <input type="text" name="qty" id="qty" class="form-control" val=""
                            placeholder="Qty">
                    </div>

                    <div class="form-group col">
                        <label>Discount</label>
                        <input type="text" name="discount" id="discount" class="form-control" placeholder="discount">
                    </div>

                    <div class="form-group col">
                        <label>Price category</label>
                        <select name="priceCategory" class="form-control" id="priceCategory">
                            <option value="retail">retail</option>
                            <option value="wholesale">wholesale</option>
                        </select>
                    </div>

                    <div class="form-group col">
                        <label>Date of sale</label>
                        <input type="date" name="date_of_sale" id="date_of_sale" class="form-control date_of_sale"
                            value="{{ date('Y-m-d') }}">
                    </div>



                    <div class="form-group col mt-4 pt-2">
                        <label></label>
                        <button type="button" id="addToCartBtn" class="btn btn-sm btn-secondary pb-2 custom-family"
                            name="AddToCart"><i class="fa f-10 fa-plus-circle pr-2"></i>Add to cart</button>
                    </div>
                </div>
            </form>
        </header>

        <div class="panel-body">

            <span class="response"></span>
            <div class="row">
                <div class="col-lg-12 text-center nunito-font">
                    @if (session('success'))
                        <div class='alert alert-success alert-dismissible' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Yello!</strong> {{ session('success') }}<i class="fa fa-check-circle"></i>
                        </div>
                    @endif

                    @if (session('fail'))
                        <div class='alert alert-danger alert-dismissible' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Sorry!</strong> {{ session('fail') }}
                        </div>
                    @endif

                </div>

            </div>

            <main>

                <div class="table table-responsive fixedTableHead" id="cart-div">
                    <table id="cart-table" class="table table-bordered cart-table">
                        <thead>
                            <tr class="warning">
                                <th>Item</th>
                                <th>Item Code</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>total</th>
                                <th>Discount/Item</th>
                                <th>Amount</th>
                                <th>Amount Paid</th>
                                <th>Is Credit</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="cart-table-body">
                        </tbody>

                      <tfoot>
                        <tr>
                            <td colspan="10"></td> <!-- empty cells for column spacing -->
                            <td>
                                <a id="printBtn" class="btn btn-sm btn-primary text-white col-md-12">
                                    <i class="fa fa-check-circle pr-2"></i> <strong class="f-15 print-btn-text">Submit Sale</strong>
                                </a>
                            </td>
                        </tr>
                      </tfoot>



                    </table>
                </div>
            </main>
            {{-- @include('pages.receipt.index') --}}
        </div>
    </div>





    <style>
        .btnRemoveAction {
            cursor: pointer !important;
        }
    </style>

    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script src="{{ asset('vendors/jquery-tabledit/jquery.tabledit.min.js') }}"></script>


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.barcode').focus();


        designCartTable();

        function designCartTable() {
            var rowCount = $('#cart-table tbody tr').length;
            if (rowCount > 0) {
                var table = $("#cart-table");
                var title = "Receipt";
                columns = [0, 1, 2, 3, 4, 5, 6];
                cartTable(table, title, columns);
            }
        }

        // $('#item-name').on('change',function(){
        //      var itemName = $('.item-name').val();
        //      var isBarcode = 0;
        //      if(itemName.length >= 3){
        //        AddItemToCart(isBarcode, itemName);
        //      }
        //      $('.item-name').val("");
        // });

        $('#addToCartBtn').on('click', function() {

            var itemName = $('.item-name').val();
            var isBarcode = 0;
            // if(itemName.length >= 3){
            AddItemToCart(isBarcode, itemName);
            // }
            $('.item-name').val("");
        });

        function getCurrentDate() {
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var year = today.getFullYear();
            today = month + '/' + day + '/' + year;
            return today;
        }

        Numberize("#qty");
        Numberize("#discount");
        Numberize(".edit_quantity");
        Numberize(".edit_discount");
        Numberize(".extra_money");


        function AddItemToCart(searchId, item) {

            var url = "{{ route('item.get') }}";

            var quantity = $('#qty').val();
            var priceCategory = $('#priceCategory').val();
            var quantity;
            (quantity) ?
            quantity = quantity: quantity = 1;


            var discount = $('#discount').val();
            (discount) ?
            discount = discount: discount = 0;

            var date_of_sale = $('#date_of_sale').val();
            (date_of_sale) ?
            date_of_sale = date_of_sale: date_of_sale = getCurrentDate();

            var inc = 0;


            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    itemId: item,
                    isBarcode: searchId,
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    console.log(dataResult);
                    var resultData = dataResult.data;
                    var bodyData = '';

                    $.each(resultData, function(index, row) {

                        var selling_price;
                        priceCategory == 'retail' ?
                            selling_price = row.selling_price :
                            selling_price = row.wholesale_price;

                        var sub_total = Convert2Num(quantity) * selling_price;
                        var sellingPrice = FormatNumber(selling_price);
                        var subTotal = FormatNumber(sub_total);
                        var total_discount = Convert2Num(quantity) * Convert2Num(discount);

                        var total = FormatNumber(sub_total - total_discount);
                        var paid = total;

                        bodyData += "<tr data-name='" + row.item + "' data-quantity='" + quantity +
                            "' data-discount='" + discount + "' data-paid='" + paid +
                            "' data-sprice='" + selling_price + "' data-date='" + date_of_sale + "'>"
                        bodyData += "<td>" + row.item + "</td><td>" + row.item_code + "</td><td>" +
                            quantity + "</td>" +
                            "<td>" + sellingPrice + "</td><td>" + subTotal + "</td><td>" + discount +
                            "</td><td>" + total + "</td><td>" + paid +
                            "</td><td><input type='checkbox' name='is_credit' id='is_credit'/> Is credit</td><td>" +
                            date_of_sale +
                            "</td><td><button class='btn btn-info btn-xs btn-edit edit-row' style='margin-left:20px;'>Edit</button>" +
                            "<button class='btn btn-danger btn-xs btn-delete delete-row' style='margin-left:20px;'>Delete</button></td>";
                        bodyData += "</tr>";

                        $('#cart-table tbody tr').each(function(i, tr) {
                            var tblItemId = $(tr).children().eq(1).text();
                            var itemQty = $(tr).children().eq(2).text();
                            var ItemPrice = $(tr).children().eq(3).text();
                            $('.barcode').val('');
                            $('.item-name').val("");
                            $('#qty').val("");
                            $('#discount').val("");
                            let newQty = Convert2Num(itemQty);

                            // if(tblItemId == row.item_code){
                            //   $(this).css({'background': '#ffa500', 'color': '#fff'});
                            // }

                            if (tblItemId == row.item_code) {
                                newQty = Convert2Num(itemQty) + quantity;

                                var newSubTotal = newQty * Convert2Num(ItemPrice);
                                let newTotalDiscount = Convert2Num(newQty) * Convert2Num(
                                    discount);
                                let newTotal = newSubTotal - newTotalDiscount;
                                newTotal % 1 != 0 ? newTotal = newTotal.toFixed(2) : newTotal =
                                    newTotal;

                                $(this).children(":eq(2)").text(FormatNumber(newQty));
                                $(this).children(":eq(4)").text(FormatNumber(newSubTotal));
                                $(this).children(":eq(6)").text(FormatNumber(newTotal));
                                $(this).children(":eq(7)").text(FormatNumber(newTotal));
                                updateSubTotal();
                                ComputeBalance();
                                inc += 1;
                            }


                        });

                        if (inc == 0) {
                            $("#cart-table-body").append(bodyData);
                            $('.barcode').val('');
                            $('.item-name').val("");
                            $('#qty').val("");
                            $('#discount').val("");
                            updateSubTotal();
                            ComputeBalance();
                        } else {

                        }

                    });
                }
            });
        }


        $(document).on("click", ".btn-edit", function() {
            var paid;
            var quantity = $(this).parents('tr').find('td:eq(2)').html();
            var discount = $(this).parents('tr').find('td:eq(5)').html();


            // if(parseFloat(discount) != 0){
            //   paid =  $(this).parents('tr').find("input[name='edit_amt_paid']").val();
            // }else{
            paid = $(this).parents('tr').attr('data-paid');
            // }
            var date_of_sale = $(this).parents('tr').attr('data-date');

            var $checkbox = $(this).parents('tr').find('input[type="checkbox"]');
            var status;
            if ($checkbox.length) {
                status = $checkbox.prop('checked');
            }

            $(this).parents('tr').find('td:eq(2)').html(
                '<input name="edit_quantity" class="edit_quantity" value="' + quantity + '" style="width:80px">'
            );
            $(this).parents('tr').find('td:eq(5)').html(
                '<input name="edit_discount" class="edit_discount" value="' + discount +
                '"  style="width:80px"> ');
            $(this).parents('tr').find('td:eq(7)').html(
                '<input name="edit_amt_paid" class="edit_amt_paid" value="' + paid + '"  style="width:80px"> ');

            $(this).parents('tr').find('td:eq(8)').html(
                '<input type="checkbox" name="is_credit" id="is_credit"> Is credit');
            $(this).parents('tr').find('td:eq(9)').html('<input type="date" name="edit_date" value="' +
                date_of_sale + '" style="width:135px">');
            $(this).parents('tr').find('td:eq(10)').prepend(
                '<button class="btn btn-info btn-xs btn-update">Update</button><button class="btn btn-warning ml-3 btn-xs btn-cancel">Cancel</button>'
            );
            // $('#is_credit').prop('checked', status);
            $(this).parents('tr').find('input[type="checkbox"]').prop('checked', status);

            $(this).hide();
            $('.btn-delete').hide();

        });

        $(document).on("click", ".btn-update", function() {

            var name = $(this).parents('tr').attr('data-name');
            var quantity = $(this).parents('tr').find("input[name='edit_quantity']").val();
            var quantity = $(this).parents('tr').find("input[name='edit_quantity']").val();
            var discount = $(this).parents('tr').find("input[name='edit_discount']").val();
            var date_of_sale = $(this).parents('tr').find("input[name='edit_date']").val();
            var paid_amount;

            var checkbox = $(this).parents('tr').find('input[type="checkbox"]');
            var status;
            if (checkbox.length) {
                status = checkbox.prop('checked');
            }
            var sprice = $(this).parents('tr').find('td:eq(3)').text();
            var newSubTotal = Convert2Num(quantity) * Convert2Num(sprice);
            var newTotalDiscount = Convert2Num(quantity) * Convert2Num(discount);
            var newTotal = newSubTotal - newTotalDiscount;

            newTotal % 1 != 0 ? newTotal = newTotal.toFixed(2) : newTotal = newTotal;

            $(this).parents('tr').find('td:eq(2)').html(quantity);
            $(this).parents('tr').find('td:eq(5)').html(discount);
            $(this).parents('tr').find('td:eq(4)').html(FormatNumber(newSubTotal));
            $(this).parents('tr').find('td:eq(6)').html(FormatNumber(newTotal));

            if (status == true) {
                paid_amount = $(this).parents('tr').find("input[name='edit_amt_paid']").val();
                $(this).parents('tr').find('td:eq(7)').html(FormatNumber(paid_amount));
            } else {
                paid_amount = newTotal;
                $(this).parents('tr').find('td:eq(7)').html(FormatNumber(newTotal));
            }

            $(this).parents('tr').find('td:eq(8)').html(
                '<input type="checkbox" name="is_credit" id="is_credit"> Is credit');
            $(this).parents('tr').find('td:eq(9)').html(date_of_sale);
            $(this).parents('tr').attr('data-quantity', quantity);
            $(this).parents('tr').attr('data-discount', discount);
            $(this).parents('tr').attr('data-paid', newTotal);

            $(this).parents('tr').find('input[type="checkbox"]').prop('checked', status);
            // $('#is_credit').prop('checked', status);
            updateSubTotal();
            ComputeBalance();

            $(this).parents('tr').find('.btn-edit').show();
            $('.btn-delete').show();
            $(this).parents('tr').find('.btn-update').hide();
            $(this).parents('tr').find('.btn-cancel').hide();


        });

        $(document).on("click", ".btn-cancel", function() {

            let quantity = $(this).parents('tr').attr('data-quantity');
            let discount = $(this).parents('tr').attr('data-discount');
            let amount_paid = $(this).parents('tr').attr('data-paid');
            let date_of_sale = $(this).parents('tr').attr('data-date');



            $(this).parents('tr').find('td:eq(2)').html(quantity);
            $(this).parents('tr').find('td:eq(5)').html(discount);
            $(this).parents('tr').find('td:eq(7)').html(amount_paid);
            $(this).parents('tr').find('td:eq(8)').html(date_of_sale);



            $(this).parents('tr').find('.btn-update').hide();
            $(this).parents('tr').find('.btn-cancel').hide();
            $(this).parents('tr').find('.btn-edit').show();
            $(this).parents('tr').find('.btn-delete').show();

        });

        $(document).on("click", ".btn-delete", function() {
            $(this).parent().parent('tr').remove();
            updateSubTotal();
            ComputeBalance();
        });

        $("#emptyCart").on("click", function() {
            EmptyCartTable();
            ComputeBalance();
        });

        // $('.barcode').on('change',function(){
        //        var barcode = $('.barcode').val();
        //        var isBarcode = 1;
        //        if(barcode.length >= 13){
        //          AddItemToCart(isBarcode, barcode);
        //        }
        //   });


        $(document).keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == 13) {
                var barcode = $('.barcode').val();
                var isBarcode = 1;
                if (barcode.length >= 13) {
                    AddItemToCart(isBarcode, barcode);
                }
            }
        });


        // $(document).keydown(function(event){
        //   var key = event.keyCode || event.charCode;
        //   if(key == 13 || key == '13' ){
        //     var table = document.getElementById('cart-table');
        //     var rowCount = (table.rows.length - 1);
        //     if(rowCount > 0){
        //       PrintReceipt();
        //     }else{
        //       alert('cart is empty');
        //     }

        //   }
        // });


        $('#printBtn').on('click', function() {
            let table = document.getElementById('cart-table-body');
            let rowCount = table.rows.length;
            if (rowCount > 0) {
                if (confirm("Are you sure you want to submit this sale?")) {
                    PrintReceipt();
                } else {
                    // do nothing
                }
            } else {
                alert('Add items to the cart');
            }
        });

        function EmptyCartTable() {
            $("#cart-table > tbody").empty();
            $(".tendered").val("");
            $('.balance').val("");
            $('.item-name').val("");
            updateSubTotal();
            $(".barcode").focus();
        }


        function PrintReceipt() {

            var TableData = new Array();
            var credit_arr = [];

            $('#cart-table tbody tr').each(function(row, tr) {
                var $chkbox = $(this).find('input[type="checkbox"]');
                var status;
                if ($chkbox.length) {
                    status = $chkbox.prop('checked');
                }
                credit_arr.push(status);
                TableData[row] = {
                    "item": $(tr).find('td:eq(0)').text(), // Name.
                    "barcode": $(tr).find('td:eq(1)').text(), // Barcode
                    "quantity": $(tr).find('td:eq(2)').text(), // Quantity
                    "price": $(tr).find('td:eq(3)').text(), // Price
                    "subtotal": $(tr).find('td:eq(4)').text(), // Subtotal
                    "discount": $(tr).find('td:eq(5)').text(), // Price
                    "total": $(tr).find('td:eq(6)').text(), // Subtotal
                    "paid": $(tr).find('td:eq(7)').text(), // Subtotal
                    "is_credit": status, // Is credit
                    "date_of_sale": $(tr).find('td:eq(9)').text(), // Subtotal

                }
            });

            let customer = $("#customer").val();
            let cashier = $("#cashier").val();
            let extra_money = $("#extra_money").val();

            if (extra_money) {
                extra_money = parseFloat(extra_money.replace(/,/g, ''));
            }

            if (credit_arr.includes(true) && !customer) {
                alert("Enter the customer name to cater for the items being taken on credit.");
            } else if (!cashier) {
                alert("Enter the person who has worked on the sale");
            } else {

                var cart_data = JSON.stringify(TableData);
                console.log("Table data", cart_data);
                $('.print-btn-text').html("saving...");

                var postUrl = "{{ route('sale.record') }}";
                $.ajax({
                    type: 'POST',
                    url: postUrl,
                    data: {
                        tabledata: cart_data,
                        customer: customer,
                        cashier: cashier,
                        extra_money: extra_money,
                    },
                    success: function(data) {
                        EmptyCartTable();
                        $("#customer").val('');
                        $("#extra_money").val('');
                        var user = "{{ Auth::user()->name }}";
                        $("#cashier").val(user);
                        var message = data.response;
                        $('.print-btn-text').html("Submit Sale");
                        updateSubTotal();
                    },
                    error: function(data) {
                        console.log(data);
                        var message = data.response;
                        console.log(message);
                        ShowResponse('.response', message, 'error');
                    }
                });
            }

        }

        var query = $('#item-name').val();
        $("#item-name").typeahead({
            source: function(query, result) {
                $.ajax({
                    url: "{{ Route('item.search') }}",
                    method: 'post',
                    data: {
                        query: query,
                    },
                    dataType: 'json',
                    success: function(data) {
                        result($.map(data, function(item) {
                            return item;
                        }));
                    },
                    error: function(data) {
                        console.log(data);
                    },
                });
            }
        });



        function ShowResponse(area, message, errorType) {
            $(area).notify(message, {
                className: errorType,
                autoHide: true,
                clickToHide: true,
                autoHideDelay: 45000,
            });
        }

        $("#UpdateCartForm").submit(function(e) {
            return false;
        });

        var query = $('#item').val();


        // var fnf = document.getElementById("tendered");
        // fnf.addEventListener('keyup', function(evt){
        //  var n = parseInt(this.value.replace(/\D/g,''), 10);
        //  $(this).val(n.toLocaleString());
        //  ComputeBalance();
        // });

        $(document).on("keyup", ".tendered", function() {
            if (this.value.length > 0) {
                var n = parseInt(this.value.replace(/\D/g, ''), 10);
                $(this).val(n.toLocaleString());
                ComputeBalance();
            } else {
                var paymentString = document.getElementById('amountToPay').innerHTML;
                if (paymentString.length > 0 && paymentString != '0') {
                    let letCustomerPay = "-" + paymentString;
                    $('.balance').val(letCustomerPay);
                    $('.balance').css("color", "red");
                }
            }
        });

        $(document).on('change', '.payment', function() {
            ComputeBalance();
        });


        function ComputeBalance() {

            var tenderedMoneyStr = $(".tendered").val();
            var paymentStr = document.getElementById('amountToPay').innerHTML;

            if (tenderedMoneyStr.length > 0) {

                var tenderedmoney = tenderedMoneyStr.replace(/,/g, '').trim();
                var payment = paymentStr.replace(/,/g, '').trim();
                var balance = (parseFloat(tenderedmoney) - parseFloat(payment));
                (balance < 0) ? $('.balance').css("color", "red"): $('.balance').css("color", "blue");
                var balanceStr = FormatNumber(balance);
                $('.balance').val(balanceStr);

            } else {
                if (paymentStr.length > 0 && paymentStr != '0') {
                    let letCustomerPay = "-" + paymentStr;
                    $('.balance').val(letCustomerPay);
                    $('.balance').css("color", "red");
                }
            }

        }

        //Computation of how much the customer must pay

        function updateSubTotal() {
            var table = document.getElementById('cart-table-body');
            let subTotal = Array.from(table.rows).reduce((total, row) => {
                var Total = row.cells[7].innerHTML;
                var subTotl = Total.replace(/,/g, '').trim();
                return total + parseFloat(subTotl);
            }, 0);

            var rowCount = (table.rows.length - 1);
            document.getElementById('num').innerHTML = FormatNumber(rowCount.toFixed(0));
            document.getElementById('amountToPay').innerHTML = FormatNumber(subTotal.toFixed(2));
        }

        function Numberize(i) {
            // $(document).on("keyup", i , function(){
            //   if(this.value.length > 0){
            //     var n = parseInt(this.value.replace(/\D/g,''), 10);
            //     $(this).val(n.toLocaleString());
            //   }
            // });
            $(document).on("keyup", i, function(event) {
                if (event.which >= 37 && event.which <= 40) {
                    event.preventDefault();
                }
                $(this).val(function(index, value) {
                    value = value.replace(/,/g, ''); // remove commas from existing input
                    return numberWithCommas(value); // add commas back in
                });
            });

        }

        function formatString2Number(num) {
            var number = num.replace(/,/g, '').trim();
            var FormattedNumber = parseFloat(number).toLocaleString('us', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return FormattedNumber;
        }


        function FormatNum(number) {
            var FormattedNumber = parseFloat(number).toLocaleString('us', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return FormattedNumber;
        }

        function FormatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

        function SanitizeString(str) {
            var newStr = str.replace(/,/g, '').trim();
            return newStr;
        }

        function Convert2Num(str) {
            var numStr;
            (str.length > 3) ?
            numStr = str.replace(/,/g, '').trim(): numStr = str;
            return parseFloat(numStr);
        }
    </script>
@endsection
