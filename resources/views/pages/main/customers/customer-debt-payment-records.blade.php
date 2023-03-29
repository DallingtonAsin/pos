@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-tile">
                <div class="row">
                    <span class="response"></span>
                    <div class="col-lg-10">
                        <h6 class="text-dark">
                            <i class="fa fa-home text-success"> /</i>
                            <strong>Customers Debt Payment Records</strong>
                            <span class="badge nunito-font">
                                @isset($total_records)
                                    {{ number_format($total_records) }}
                                @endisset
                            </span>
                            </span>
                        </h6>
                    </div>

                    <div class="col-lg-2">
                        <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                            id="addNewPayment"><i class="fa fa-plus-circle pr-1"></i>Add payment</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive custom-family">
                <table class="table table-bordered table-hover debt-payment-records-table" id="debt-payment-records-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                </table>
            </div>


            <div class="modal fade nunito-font addPaymentModal" id="addPaymentModal" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form name="customers" id="PaymentForm">
                            @csrf
                            <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">
                                    Debt Details for the customer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <span><span class="text-danger pr-1">*</span>Customer</span>
                                    <input type="hidden" class="form-control paymentId" name="paymentId">
                                    <select class="form-control customer" name="customer">
                                        <option value="">Select customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <span><span class="text-danger pr-1">*</span>Debt Amount</span>
                                    <input type="text" class="form-control debt_amount text-danger" name="debt_amount"
                                        placeholder="Debt" readonly>
                                </div>

                                <div class="form-group">
                                    <span><span class="text-danger pr-1">*</span>Paid Amount</span>
                                    <input type="text" class="form-control paid_amount " name="paid_amount"
                                        placeholder="Paid amount">
                                </div>

                                <div class="form-group">
                                    <span><span class="text-danger pr-1">*</span>Balance</span>
                                    <input type="text" class="form-control balance" name="balance" placeholder="Balance"
                                        readonly>
                                </div>

                                <div class="form-group date">
                                    <span><span class="text-danger pr-1">*</span>Payment Date</span>
                                    <input type="date" class="form-control payment_date" name="payment_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success submitBtn" name="submitBtn">Save</button>
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
        const ajaxUrl = @json(route('customers.debts.payments.ajax'));
        const deletedSeletectedUrl = @json(route('selected-customers.remove'));
        const cat = 'customer-debt-payment-records';
        const token = "{{ csrf_token() }}";
        let table = $('#debt-payment-records-table');
        let title = "Customers debt payment records";
        let columns = [1, 2, 3, 4, 5];
    </script>


    @can('isAdmin')
        <script>
            let dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'date',
                    name: 'date'
                },

                {
                    data: 'paid_amount',
                    name: 'paid_amount'
                },
                {
                    data: 'balance',
                    name: 'balance'
                },
                {
                    data: 'recorded_by',
                    name: 'recorded_by'
                },
            ];
            makeDataTable2(table, title, columns, dataColumns);
        </script>
    @endcan

    @can('isCashier')
        <script>
            let dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'date',
                    name: 'date'
                },

                {
                    data: 'paid_amount',
                    name: 'paid_amount'
                },
                {
                    data: 'balance',
                    name: 'balance'
                },
                {
                    data: 'recorded_by',
                    name: 'recorded_by'
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

            Numberize(".paid_amount");
            Numberize(".debt_amount");

            onSelectCustomer();

            function readableValue(value) {
                if (value.length > 0) {
                    let n = parseInt(value.replace(/\D/g, ''), 10);
                    return n.toLocaleString();
                }
                return value;
            }

            function convert2Num(numStr) {
                const numberString = numStr.replace(',', '');
                const number = parseFloat(numberString);
                return number;
            }

            function Numberize(i) {
                $(document).on("keyup", i, function() {
                    let val = readableValue(this.value);
                    $(this).val(val);
                });
            }

            $('#addNewPayment').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.submitBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.paymentId').val('');
                $('#PaymentForm').trigger("reset");
                $('#modalHeading').html("Add new payment");
                $('#addPaymentModal').modal('show');
            });

            function onSelectCustomer() {
                $('.customer').on('change', function() {
                    let customer_id = $(this).find(":selected").val();
                    if (customer_id) {
                        populateCustomerDebt(customer_id);
                    }
                });
            }

            function populateCustomerDebt(customer_id) {
                let url = "{{ route('customer.debt.ajax', ':id') }}";
                url = url.replace(':id', customer_id);
                $.get(url, function(response) {
                    if (response.success) {
                        let debt = response.data;
                        let val = readableValue(debt.toString());
                        $('.debt_amount').val(val);
                    } else {
                        ShowResponse('.response', response.error, 'error');
                    }
                });
            }

            $('body').on('input', '.paid_amount', function(event) {
                let debt, paid_amount, balance;
                debt = $('.debt_amount').val();
                paid_amount = $('.paid_amount').val();
                balance = convert2Num(debt) - convert2Num(paid_amount);
                if (balance > debt) {
                    $(".balance").addClass("text-danger");
                } else {
                    $(".balance").addClass("text-success");
                }
                let balanceStr = readableValue(balance.toString())
                $('.balance').val(balanceStr);
            });


            //modal used to edit customer details [each row of the tbl]
            $('body').on('click', '#edit-sale', function(event) {
                let sale_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('customers.with.debts') }}" + '/' + sale_id + '', function(data) {

                    $('#modalHeading').html("Edit sale debt details of customer " + data.customer +
                        "");
                    $('.submitBtn').text("Edit details");
                    $('#addPaymentModal').modal('show');
                    $('.sale_id').val(data.id);
                    $('.name').val(data.customer);
                    $('.item_taken').val(data.item);
                    $('.quantity').val(data.quantity);
                    $('.amount').val(data.amount);
                    $('.discount').val(data.discount);
                    $('.amount_paid').val(data.amount_paid);
                    $('.balance').val(data.balance);
                    $('.taken_on').val(data.date);
                    $('.received-div').show();
                    DisableTableFields(true, false);
                    ShowBtns();
                })
            });


            //View Modal used to view each row [customer debt details]
            $('body').on('click', '#view-sale', function(event) {
                let sale_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('customers.with.debts') }}" + '/' + sale_id + '', function(data) {
                    console.log("Data is", data);

                    $('#modalHeading').html("Debt details for customer " + data.customer + "");
                    $('#addPaymentModal').modal('show');

                    $('.name').val(data.customer);
                    $('.item_taken').val(data.item);
                    $('.quantity').val(data.quantity);
                    $('.amount').val(data.amount);
                    $('.discount').val(data.discount);
                    $('.amount_paid').val(data.amount_paid);
                    $('.balance').val(data.balance);
                    $('.taken_on').val(data.date);
                    $('.received-div').hide();

                    DisableTableFields(true, true);
                    HideBtns();
                })
            });


            $('.submitBtn').click(function(e) {

                e.preventDefault();

                let Errors = validateForm();
                if (Errors.length == 0) {
                    $(this).html('Updating..');

                    $.ajax({
                        data: $('#PaymentForm').serialize(),
                        url: "{{ route('customer-debt-payments.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {

                            $('#PaymentForm').trigger("reset");
                            $('#addPaymentModal').modal("hide");

                            let message = data.success || data.error;
                            let type = data.success ? 'success' : 'error';

                            ShowResponse('.response', message, type);
                            let tbl = $('#debt-payment-records-table').DataTable();
                            tbl.ajax.reload();

                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            ShowResponse('.response', data.error, 'error');
                            $('.submitBtn').html('Edit details');
                        }
                    });
                } else {
                    let i;
                    let message = "";
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
                $('.amount_paid').attr('disabled', bool);
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
                let FormattedNumber = parseFloat(number).toLocaleString('us', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                return FormattedNumber;
            }

            function ResetTblInfo(response) {
                let total_debtors, total_debts;
                total_debtors = FormatNumber(response.total_debtors);
                total_debts = FormatNumber(response.total_debts);

                $('.total_debtors').html(total_debtors);
                $('.total_debts').html(total_debts);
            }

            function validateForm() {
                let customer = $('.customer').val();
                let paid_amount = $('.paid_amount').val();
                let payment_date = $('.payment_date').val();

                let errors = [];
                if (customer.length < 1) {
                    errors.push("Please select customer");
                }

                if (paid_amount.length < 1) {
                    errors.push("Please enter payment date");
                }

                if (payment_date.length < 1) {
                    errors.push("Please select payment date");
                }
                return errors;
            }



        });
    </script>
@endsection
