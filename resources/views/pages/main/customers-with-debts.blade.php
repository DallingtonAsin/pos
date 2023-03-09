@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-tile">

                <div class="row nunito-font">
                    <span class="response"></span>
                    <div class="col-lg-4">
                        <h5 class="text-dark">
                            <i class="fa fa-home text-success"> /</i>
                            <strong>Customers with debts</strong>
                        </h5>
                    </div>

                    @can('isAdmin')
                        <div class="col-lg-4">
                            <h5>
                                <strong>Current Total Debts: UGX. </strong><label class="text-danger total_debts">
                                    @isset($total_debts)
                                        {{ number_format($total_debts) }}
                                    @endisset
                                </label>
                            </h5>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="panel-body">

            <div class="row">
                <div class="col-lg-8 text-center nunito-font">

                    @if (session()->get('success'))
                        <div class='alert alert-success alert-dismissible' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Yello!</strong> {{ session()->get('success') }}<i class="fa fa-check-circle"></i>
                        </div>
                    @endif

                    @if (session()->get('fail'))
                        <div class='alert alert-danger alert-dismissible' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Oops!</strong> {{ session()->get('fail') }}
                        </div>
                    @endif

                </div>
            </div>

            <div class="table-responsive custom-family">

                <table class="table table-bordered table-hover customers-with-debts-table" id="customers-with-debts-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Debt</th>
                        </tr>
                    </thead>
                </table>
            </div>


            <div class="modal fade nunito-font CustomerDebtDetailsModel" id="CustomerDebtDetailsModel" tabindex="-1"
                role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <form name="customers" id="DebtSaleDetailsForm">
                            @csrf
                            <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Debt Details for the
                                    customer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <span>Name</span>
                                    <input type="hidden" class="form-control sale_id " name="id">
                                    <input type="text" class="form-control name " name="name">
                                </div>


                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <span>Item taken</span>
                                        <input type="text" class="form-control item_taken " name="item_taken">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <span>Quantity</span>
                                        <input name="quantity" class="form-control  quantity" id="quantity">
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <span>Discount</span>
                                        <input type="text" class="form-control discount " name="discount">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <span>Taken on</span>
                                        <input type="date" value="" class="form-control taken_on " id="taken_on"
                                            name="taken_on">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <span>Amount</span>
                                        <input type="text" class="form-control amount " name="amount">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <span>Paid Amount</span>
                                        <input type="text" class="form-control paid_amount " name="paid_amount">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <span>Balance</span>
                                    <input type="text" class="form-control balance  text-danger" name="balance" required>
                                </div>



                                <div class="form-group">
                                    <button type="submit" class="btn btn-success submitBtn"
                                        name="submitBtn">Save</button>

                                </div>

                                <div class="form-group">
                                    <span class="errors-section text-danger nunito-font"></span>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>




        </div>
    </div>





    <script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('customers.with.debts.ajax'));
        const deletedSeletectedUrl = @json(route('selected-customers.remove'));
        const cat = 'customers-with-debts';
        const token = "{{ csrf_token() }}";
        var table = $('#customers-with-debts-table');
        var title = "List of customers with debts in the system";
        var columns = [0, 1, 2];
    </script>


    @can('isAdmin')
        <script>
            var dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'customer_contact',
                    name: 'customer_contact'
                },
                {
                    data: 'debt',
                    name: 'debt'
                }
            ];
            makeDataTable2(table, title, columns, dataColumns);
        </script>
    @endcan

    @can('isCashier')
        <script>
            var dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'customer_contact',
                    name: 'customer_contact'
                },
                {
                    data: 'debt',
                    name: 'debt'
                },
            ];
            makeDataTable2(table, title, columns, dataColumns);
        </script>
    @endcan


    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            Numberize(".balance");

            function Numberize(i) {
                $(document).on("keyup", i, function() {
                    if (this.value.length > 0) {
                        var n = parseInt(this.value.replace(/\D/g, ''), 10);
                        $(this).val(n.toLocaleString());
                    }
                });
            }

            $('body').on('input', '.received', function(event) {
                let cost, last_paid, received, balance, outstanding_bal;
                cost = parseFloat($('.amount').val());
                last_paid = parseFloat($('.paid_amount').val());
                received = parseFloat($('.received').val());
                outstanding_bal = cost - last_paid;
                if (received > outstanding_bal) {
                    alert("Cannot accept value greater than customer's outstanding balance " +
                        outstanding_bal + "")
                } else {
                    balance = cost - (last_paid + received);
                }
                $('.balance').val(balance);
            });


            //modal used to edit customer details [each row of the tbl]
            $('body').on('click', '#edit-sale', function(event) {
                var sale_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('customers.with.debts') }}" + '/' + sale_id + '', function(data) {

                    $('#modalHeading').html("Edit sale debt details of customer " + data.customer +
                        "");
                    $('.submitBtn').text("Edit details");
                    $('#CustomerDebtDetailsModel').modal('show');
                    $('.sale_id').val(data.id);
                    $('.name').val(data.customer);
                    $('.item_taken').val(data.item);
                    $('.quantity').val(data.quantity);
                    $('.amount').val(data.amount);
                    $('.discount').val(data.discount);
                    $('.paid_amount').val(data.paid_amount);
                    $('.balance').val(data.balance);
                    $('.taken_on').val(data.date);
                    $('.received-div').show();
                    DisableTableFields(true, false);
                    ShowBtns();
                })
            });


            //View Modal used to view each row [customer debt details]
            $('body').on('click', '#view-sale', function(event) {
                var sale_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('customers.with.debts') }}" + '/' + sale_id + '', function(data) {
                    console.log("Data is", data);

                    $('#modalHeading').html("Debt details for customer " + data.customer + "");
                    $('#CustomerDebtDetailsModel').modal('show');

                    $('.name').val(data.customer);
                    $('.item_taken').val(data.item);
                    $('.quantity').val(data.quantity);
                    $('.amount').val(data.amount);
                    $('.discount').val(data.discount);
                    $('.paid_amount').val(data.paid_amount);
                    $('.balance').val(data.balance);
                    $('.taken_on').val(data.date);
                    $('.received-div').hide();

                    DisableTableFields(true, true);
                    HideBtns();
                })
            });


            $('.submitBtn').click(function(e) {

                e.preventDefault();

                var Errors = validateForm();
                if (Errors.length == 0) {
                    $(this).html('Updating..');

                    $.ajax({
                        data: $('#DebtSaleDetailsForm').serialize(),
                        url: "{{ route('customer.debt.update') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            $('#DebtSaleDetailsForm').trigger("reset");
                            $('#CustomerDebtDetailsModel').modal("hide");
                            var resp = data.success;
                            console.log("Got this message for you", resp);
                            ShowResponse('.response', resp, 'success');
                            ResetTblInfo(data);
                            var tbl = $('#customers-with-debts-table').DataTable();
                            tbl.ajax.reload();

                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            ShowResponse('.response', data.error, 'error');
                            $('.submitBtn').html('Edit details');
                        }
                    });
                } else {
                    var i;
                    var message = "";
                    for (i = 0; i < Errors.length; i++) {
                        message += Errors[i] + "<br>";
                    }
                    $('.errors-section').html(message);

                }

            });




            function DisableTableFields(bool, boolX) {
                $('.name').attr('disabled', bool);
                $('.item_taken').attr('disabled', bool);
                $('.quantity').attr('disabled', bool);
                $('.amount').attr('disabled', bool);
                $('.discount').attr('disabled', bool);
                $('.paid_amount').attr('disabled', bool);
                $('.balance').attr('disabled', bool);
                $('.taken_on').attr('disabled', bool);

            }

            function HideBtns() {
                $('.submitBtn').hide();
                $('.closeBtn').hide();
            }

            function ShowBtns() {
                $('.submitBtn').show();
                $('.closeBtn').show();
            }

            function ShowResponse(area, message, errorType) {
                $(area).notify(message, {
                    className: errorType,
                    autoHide: true,
                    clickToHide: true,
                    autoHideDelay: 45000,
                });
            }

            function FormatNumber(number) {
                var FormattedNumber = parseFloat(number).toLocaleString('us', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                return FormattedNumber;
            }

            function ResetTblInfo(response) {
                var total_debtors, total_debts;
                total_debtors = FormatNumber(response.total_debtors);
                total_debts = FormatNumber(response.total_debts);

                $('.total_debtors').html(total_debtors);
                $('.total_debts').html(total_debts);
            }

            function validateForm() {
                var balance = $('.balance').val();
                var errors = [];
                if (balance.length < 1) {
                    var balanceErrr = "Please enter the balance the customer is left with!";
                    errors.push(balanceErrr);
                }
                return errors;
            }



        });
    </script>
@endsection
