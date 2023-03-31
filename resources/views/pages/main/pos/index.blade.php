@extends('layouts.master')


@section('content')
    <div class="panel panel-success" id="panel">
        <div class="panel-heading cartPanelHeader" id="panel-heading">
            <div class="panel-title nunito-font">
                <div class="row">
                    <div class="col-lg-4">
                        <i class="typcn typcn-shopping-cart"></i> cart
                        <span class="badge">
                            <span id="num">0</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <header class="mt-3 mx-4">
            <form id="CartForm" class="form">
                @csrf
                <div class="row">

                    <div class="form-group col-md-2">
                        <label>Total Cost</label>
                        <input type="text" class="form-control text-primary amountToPay" id="amountToPay" readonly
                            placeholder="Total cost of items">
                    </div>

                    {{-- <div class="form-group col-md-2">
                        <label>Tendered Amount</label>
                        <input type="text" class="form-control tendered" id='tendered' placeholder="Tendered amount">
                    </div> 
                    --}}

                    <div class="form-group col-md-2">
                        <label>Balance</label>
                        <input type="text" class="form-control  balance" readonly placeholder="Customer balance">
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
                    <div class="form-group col-md-2">
                        <label>Date of sale</label>
                        <input type="date" name="date_of_sale" id="date_of_sale" class="form-control date_of_sale"
                            value="{{ date('Y-m-d') }}">
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
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="12" style="text-align: right;">
                                    <div class="row">
                                        <div class="col-lg-6"></div>
                                        <div class="d-flex col-lg-6">

                                            <label class="pr-2 mt-3 text-danger">Taken on Credit ?</label>
                                            <select name="is_credit" class="form-control is_credit mt-2" id="is_credit"
                                                disabled>
                                                <option value="1">Yes</option>
                                                <option value="0" selected="true">No</option>
                                            </select>

                                            <input type="text" class="form-control h-90 paid_amount mt-2 mx-4"
                                                value="" id="paid_amount" placeholder="Enter paid amount"
                                                name="paid_amount">

                                            <a id="printBtn" class="btn btn-sm btn-primary text-white mx-3">
                                                <i class="fa fa-check-circle pr-2"></i> <strong
                                                    class="print-btn-text">Submit Sale</strong>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </main>
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
            let rowCount = $('#cart-table tbody tr').length;
            if (rowCount > 0) {
                let table = $("#cart-table");
                let title = "Receipt";
                columns = [0, 1, 2, 3, 4, 5, 6];
                cartTable(table, title, columns);
            }
        }

        $('#addToCartBtn').on('click', function() {
            let itemName = $('.item-name').val();
            let isBarcode = 0;
            addItemtoCart(isBarcode, itemName);
            $('.item-name').val("");
        });

        function getCurrentDate() {
            let today = new Date();
            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let year = today.getFullYear();
            today = month + '/' + day + '/' + year;
            return today;
        }

        Numberize("#qty");
        Numberize("#discount");
        Numberize(".edit_quantity");
        Numberize(".edit_discount");
        Numberize(".paid_amount");

        $('#paid_amount').on('input', function() {
            let paid_amount_str, paid_amount;
            let total_cost_str, total_cost;

            paid_amount_str = $('#paid_amount').val();
            total_cost_str = $('#amountToPay').val();

            if (paid_amount_str && total_cost_str) {
                paid_amount = convertToNumber(paid_amount_str);
                total_cost = convertToNumber(total_cost_str);

                if (paid_amount >= total_cost) {
                    $('.is_credit').val('0');
                } else {
                    $('.is_credit').val('1');
                }
            }
        });


        function addItemtoCart(searchId, item) {

            let url = "{{ route('item.get') }}";

            let qty = convertToNumber($('#qty').val());
            let priceCategory = $('#priceCategory').val();
            let quantity = qty ? qty : 1;

            let discount_amount = $('#discount').val();
            let discount = discount_amount ? discount_amount : 0;

            let date = $('#date_of_sale').val();
            let date_of_sale = date ? date : getCurrentDate();

            let inc = 0;


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
                success: function(response) {

                    let resultData = response.data;
                    let bodyData = '';

                    $.each(resultData, function(index, row) {

                        if (row.quantity >= quantity) {

                            let selling_price = (priceCategory == 'retail') ? row.selling_price : row
                                .wholesale_price;
                            let sub_total = convertToNumber(quantity) * selling_price;
                            let sellingPrice = FormatNumber(selling_price);
                            let subTotal = FormatNumber(sub_total);
                            let total_discount = convertToNumber(quantity) * convertToNumber(discount);

                            let total = FormatNumber(sub_total - total_discount);

                            bodyData += "<tr data-name='" + row.item + "' data-quantity='" + quantity +
                                "' data-discount='" + discount + "' data-sprice='" + selling_price +
                                "' data-date='" + date_of_sale +
                                "'>"
                            bodyData += "<td>" + row.item + "</td><td>" + row.item_code + "</td><td>" +
                                quantity + "</td>" +
                                "<td>" + sellingPrice + "</td><td>" + subTotal + "</td><td>" +
                                discount +
                                "</td><td>" + total + "</td><td>" + date_of_sale +
                                "</td><td><button class='btn btn-info btn-xs btn-edit edit-row' style='margin-left:20px;'>Edit</button>" +
                                "<button class='btn btn-danger btn-xs btn-delete delete-row' style='margin-left:20px;'>Delete</button></td>";
                            bodyData += "</tr>";

                            $('#cart-table tbody tr').each(function(i, tr) {

                                let tblItemName = $(tr).children().eq(0).text();
                                let tblItemId = $(tr).children().eq(1).text();
                                let itemQty = $(tr).children().eq(2).text();
                                let ItemPrice = $(tr).children().eq(3).text();

                                let newQty = convertToNumber(itemQty);

                                // if(tblItemId == row.item_code){
                                //   $(this).css({'background': '#ffa500', 'color': '#fff'});
                                // }

                                if (tblItemId == row.item_code || tblItemName == row.item) {
                                    newQty = convertToNumber(itemQty) + quantity;

                                    let newSubTotal = newQty * convertToNumber(ItemPrice);
                                    let newTotalDiscount = convertToNumber(newQty) *
                                        convertToNumber(
                                            discount);
                                    let newTotal = newSubTotal - newTotalDiscount;
                                    newTotal % 1 != 0 ? newTotal = newTotal.toFixed(2) :
                                        newTotal =
                                        newTotal;

                                    $(this).children(":eq(2)").text(FormatNumber(newQty));
                                    $(this).children(":eq(4)").text(FormatNumber(newSubTotal));
                                    $(this).children(":eq(6)").text(FormatNumber(newTotal));
                                    updateSubTotal();
                                    ComputeBalance();
                                    inc += 1;
                                }

                                $('.barcode').val('');
                                $('.item-name').val("");
                                $('#qty').val("");
                                $('#discount').val("");


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

                        } else {
                            alert(
                                `There isn't enough ${row.item} in stock. Current quantity in the system is ${row.quantity}. If you are sure there's enough ${row.item} in stock, please first update quantity in the system`
                            )
                            $('.barcode').val('');
                            $('.item-name').val("");
                            $('#qty').val("");
                            $('#discount').val("");
                        }

                    });
                }
            });
        }


        $(document).on("click", ".btn-edit", function() {

            let quantity = $(this).parents('tr').find('td:eq(2)').html();
            let discount = $(this).parents('tr').find('td:eq(5)').html();

            let date_of_sale = $(this).parents('tr').attr('data-date');

            let $checkbox = $(this).parents('tr').find('input[type="checkbox"]');
            let status;
            if ($checkbox.length) {
                status = $checkbox.prop('checked');
            }

            $(this).parents('tr').find('td:eq(2)').html(
                '<input name="edit_quantity" class="edit_quantity" value="' + quantity + '" style="width:80px">'
            );
            $(this).parents('tr').find('td:eq(5)').html(
                '<input name="edit_discount" class="edit_discount" value="' + discount +
                '"  style="width:80px"> ');
            $(this).parents('tr').find('td:eq(7)').html('<input type="date" name="edit_date" value="' +
                date_of_sale + '" style="width:135px">');
            $(this).parents('tr').find('td:eq(8)').prepend(
                '<button class="btn btn-info btn-xs btn-update">Update</button><button class="btn btn-warning ml-3 btn-xs btn-cancel">Cancel</button>'
            );


            $(this).hide();
            $('.btn-delete').show();

        });

        $(document).on("click", ".btn-update", function() {

            let name = $(this).parents('tr').attr('data-name');
            let quantity = $(this).parents('tr').find("input[name='edit_quantity']").val();
            let discount = $(this).parents('tr').find("input[name='edit_discount']").val();
            let date_of_sale = $(this).parents('tr').find("input[name='edit_date']").val();

            let sprice = $(this).parents('tr').find('td:eq(3)').text();
            let newSubTotal = convertToNumber(quantity) * convertToNumber(sprice);
            let newTotalDiscount = convertToNumber(quantity) * convertToNumber(discount);
            let newTotal = newSubTotal - newTotalDiscount;

            newTotal % 1 != 0 ? newTotal = newTotal.toFixed(2) : newTotal = newTotal;

            $(this).parents('tr').find('td:eq(2)').html(quantity)
            $(this).parents('tr').find('td:eq(5)').html(discount)
            $(this).parents('tr').find('td:eq(4)').html(FormatNumber(newSubTotal))
            $(this).parents('tr').find('td:eq(6)').html(FormatNumber(newTotal))
            $(this).parents('tr').find('td:eq(7)').html(date_of_sale)
            $(this).parents('tr').attr('data-quantity', quantity)
            $(this).parents('tr').attr('data-discount', discount)
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
            let date_of_sale = $(this).parents('tr').attr('data-date');

            $(this).parents('tr').find('td:eq(2)').html(quantity);
            $(this).parents('tr').find('td:eq(5)').html(discount);
            $(this).parents('tr').find('td:eq(7)').html(date_of_sale);
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


        $(document).keypress(function(event) {
            let keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == 13) {
                let barcode = $('.barcode').val();
                let isBarcode = 1;
                if (barcode.length >= 13) {
                    addItemtoCart(isBarcode, barcode);
                }
            }
        });


        // $(document).keydown(function(event){
        //   let key = event.keyCode || event.charCode;
        //   if(key == 13 || key == '13' ){
        //     let table = document.getElementById('cart-table');
        //     let rowCount = (table.rows.length - 1);
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
                PrintReceipt();
            } else {
                alert('Add items to the cart');
            }
        });

        function EmptyCartTable() {
            $("#cart-table > tbody").empty();
            $(".paid_amount").val("");
            $('.balance').val("");
            $('.item-name').val("");
            updateSubTotal();
            $(".barcode").focus();
        }


        function PrintReceipt() {

            let TableData = new Array();

            $('#cart-table tbody tr').each(function(row, tr) {

                TableData[row] = {
                    "item": $(tr).find('td:eq(0)').text(),
                    "barcode": $(tr).find('td:eq(1)').text(),
                    "quantity": $(tr).find('td:eq(2)').text(),
                    "price": $(tr).find('td:eq(3)').text(),
                    "subtotal": $(tr).find('td:eq(4)').text(),
                    "discount": $(tr).find('td:eq(5)').text(),
                    "total": $(tr).find('td:eq(6)').text(),
                    "date_of_sale": $(tr).find('td:eq(9)').text()
                }
            });

            let customer = $("#customer").val();
            let amount_paid = $("#paid_amount").val();
            let is_credit = $("#is_credit").val();
            let total_cost = $('#amountToPay').val();


            if (!amount_paid) {
                alert(`Please enter amount paid by the customer`);
            } else if ((convertToNumber(total_cost) > convertToNumber(amount_paid)) && !customer) {
                alert(`Please select customer since items are being sold on credit`);
            } else {

                if (confirm("Are you sure you want to submit this sale?")) {

                    let cart_data = JSON.stringify(TableData);
                    console.log("Table data", cart_data);
                    $('.print-btn-text').html("saving...");

                    let postUrl = "{{ route('sale.record') }}";
                    $.ajax({
                        type: 'POST',
                        url: postUrl,
                        data: {
                            tabledata: cart_data,
                            customer: customer,
                            total_cost: total_cost,
                            amount_paid: amount_paid,
                            is_credit: is_credit
                        },

                        success: function(response) {

                            let message = response.success || response.error
                            let type = response.success ? 'success' : 'error'

                            if (response.success) {
                                EmptyCartTable();
                                $('.is_credit').val('0');
                                $("#customer").val('');
                                $("#extra_money").val('');
                                $('.print-btn-text').html("Submit Sale");
                                updateSubTotal();
                            }

                            displayResponse('.response', message, type);
                            $('.print-btn-text').html('Submit Sale')
                        },
                        error: function(data) {
                            console.log(data);
                            let message = data.response;
                            console.log(message);
                            displayResponse('.response', message, 'error');
                        }
                    });
                } else {
                    // transaction submission cancelled
                }
            }

        }

        let item_name = $('#item-name').val();
        $("#item-name").typeahead({
            source: function(item_name, result) {
                $.ajax({
                    url: "{{ Route('item.search') }}",
                    method: 'post',
                    data: {
                        query: item_name,
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



        function displayResponse(area, message, errorType) {
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

        $(document).on("keyup", ".paid_amount", function() {
            let paymentString = $('.amountToPay').val();
            if (this.value.length > 0) {
                let n = parseInt(this.value.replace(/\D/g, ''), 10);
                $(this).val(n.toLocaleString());
                if (paymentString.length > 0 && paymentString != '0') {
                    ComputeBalance();
                }
            } else {
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

            let paidMoneyStr = $(".paid_amount").val();
            let paymentStr = $('.amountToPay').val(); // document.getElementById('amountToPay').innerHTML;

            if (paidMoneyStr.length > 0) {

                let paid_money = paidMoneyStr.replace(/,/g, '').trim();
                let payment = paymentStr.replace(/,/g, '').trim();
                let balance = (parseInt(paid_money) - parseInt(payment));
                (balance < 0) ? $('.balance').css("color", "red"): $('.balance').css("color", "blue");
                let balanceStr = FormatNumber(balance);
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
            let table = document.getElementById('cart-table-body');
            let subTotal = Array.from(table.rows).reduce((total, row) => {
                let Total = row.cells[6].innerHTML;
                let subTotl = Total.replace(/,/g, '').trim();
                return total + parseInt(subTotl);
            }, 0);

            let rowCount = table.rows.length;
            document.getElementById('num').innerHTML = FormatNumber(rowCount.toFixed(0));
            $('.amountToPay').val(FormatNumber(subTotal.toFixed(2)));
            //document.getElementById('amountToPay').innerHTML = FormatNumber(subTotal.toFixed(2));
        }

        function Numberize(i) {
            $(document).on("keyup", i, function(event) {
                if (event.which >= 37 && event.which <= 40) {
                    event.preventDefault();
                }
                $(this).val(function(index, value) {
                    value = value.replace(/,/g, '');
                    return numberWithCommas(value);
                });
            });

        }

        function formatString2Number(num) {
            let number = num.replace(/,/g, '').trim();
            let FormattedNumber = parseInt(number).toLocaleString('us', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return FormattedNumber;
        }


        function FormatNum(number) {
            let FormattedNumber = parseInt(number).toLocaleString('us', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return FormattedNumber;
        }

        function FormatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        function numberWithCommas(x) {
            let parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

        function SanitizeString(str) {
            let newStr = str.replace(/,/g, '').trim();
            return newStr;
        }

        function convertToNumber(str) {
            let numStr;
            numStr = (str.length > 3) ? str.replace(/,/g, '').trim() : str;
            return parseInt(numStr);
        }
    </script>
@endsection
