@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading d-flex align-items-center">
            <span class="response"></span>
            <div class="col-md-5">
                <h5 class="panel-title mb-0 text-dark">
                    <i class="fa fa-home text-success"> /</i>
                    <strong>Supplier Credits</strong>
                    <strong class="badge badge-info no_of_credits">
                        @isset($no_of_credits)
                            {{ number_format($no_of_credits) }}
                        @endisset
                    </strong>
                </h5>
            </div>

            <div class="col-md-4">
                <h5 class="panel-title mb-0 text-dark">
                    <strong>Total Credits: UGX.</strong>
                    @isset($total_credits)
                        <strong class="text-success total_credits"> {{ number_format($total_credits) }}</strong>
                    @endisset
                </h5>
            </div>

            <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                id="addSupplierCredit"><i class="fa fa-plus-circle pr-1"></i>Add supplier credit</button>
        </div>

        <div class="panel-body">

            <div class="table table-sm table-responsive">
                <table class="table table-bordered table-hover supplier-credits-table" id="supplier-credits-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Credit Amount</th>
                            <th>Date</th>
                            <th>is Deleted</th>
                            <th>Added By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--Add supplier credit -->
    <div class="modal fade nunito-font addSupplierCreditModal" id="addSupplierCreditModal" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true"
        role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form name="SupplierCreditsForm" id="SupplierCreditsForm">
                    @csrf
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new supplier credit</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" class="form-control credit_id  credit_id" name="id"
                                placeholder="Enter credit id">
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i> Supplier</span>
                            <select class="form-control supplier" name="supplier">
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i> Credit Amount</span>
                            <input type="text" class="form-control amount" name="amount" placeholder="Enter amount">
                        </div>

                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span>Date</span>
                            <input type="date" class="form-control date " value="{{ date('Y-m-d') }}" name="date"
                                placeholder="Select date">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary rounded-pill addSupplierCreditBtn"
                                name="addSupplierCreditBtn">Save</button>
                            <button type="reset" class="btn btn-danger rounded-pill clearBtn">Clear</button>
                        </div>

                        <div class="form-group">
                            <span class="errors-section text-danger nunito-font"></span>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--Modal Delete Stores -->
    <div class="modal fade" id="deleteSupplierCreditModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog"
        aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h6 class="modal-title delete-modal-title w-100 font-weight-bold">Delete supplier credit</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger delete-alert-text">Are you sure you want to delete this supplier
                                credit
                                <small class="text-dark text-muted bolded">
                                </small>
                                ?
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary rounded-pill delete-ok-btn"
                            name="ConfirmBtn">Yes</button>
                        <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end of modal Delete Stores-->

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('suppliers.credits.ajax'));
        const deletedSeletectedUrl = @json(route('selected-suppliers.remove'));
        const cat = 'supplier_credits';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            let table = $('#supplier-credits-table');
            let title = "List of supplier credits in the system";
            let columns = [0, 1, 2, 3, 4];
            let dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'supplier',
                    name: 'supplier'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'is_deleted',
                    name: 'is_deleted'
                },
                {
                    data: 'added_by',
                    name: 'added_by'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ];

            makeDataTable2(table, title, columns, dataColumns);

            $('.modal').on('hidden.bs.modal', function() {
                $('.credit_id').val('');
            });

            Numberize('.amount');

            $('#addSupplierCredit').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.addSupplierCreditBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.credit_id').val('');
                $('#SupplierCreditsForm').trigger("reset");
                $('#modalHeading').html("Add new supplier credit");
                $('#addSupplierCreditModal').modal('show');
            });


            $('.addSupplierCreditBtn').click(function(e) {

                e.preventDefault();
                let credit_id = $('.credit_id').val();
                let method, url;

                if (credit_id) {
                    url = "{{ route('supplier-credits.update', ':id') }}";
                    url = url.replace(":id", credit_id);
                    method = 'PUT';
                } else {
                    url = "{{ route('supplier-credits.store') }}";
                    method = 'POST';
                }

                let isValidForm = validateForm();

                if (isValidForm) {
                    $(this).html('Sending..');

                    $.ajax({
                        data: $('#SupplierCreditsForm').serialize(),
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(response) {

                            let message = response.success || response.error;
                            let type = response.success ? 'success' : 'error';

                            if (response.success) {
                                let data = response.data;
                                $('.credit_id').val('');
                                $('#SupplierCreditsForm').trigger("reset");
                                $('#addSupplierCreditModal').modal("hide");
                                resetTblInfo(data);
                                let tbl = $('#supplier-credits-table').DataTable();
                                tbl.ajax.reload();
                            }

                            displayResponse('.response', message, type);
                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            displayResponse('.response', data.error, 'error');
                            $('.addSupplierCreditBtn').html('Save Changes');
                        }
                    });
                }

            });

            //modal used to edit supplier credit details [each row of the tbl]
            $('body').on('click', '#edit-supplier-credit', function(event) {
                let credit_id = $(this).data('id');
                event.preventDefault();
                editStore(credit_id);
            });

            function editStore(credit_id) {
                $('.credit_id').val(credit_id);
                $.get("{{ route('supplier-credits.index') }}" + '/' + credit_id + '/edit', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Edit credit details for supplier " + data.supplier + "");
                        $('.addSupplierCreditBtn').text("Update");
                        $('#addSupplierCreditModal').modal('show');
                        $('.credit_id').val('');
                        populateStoreDetails(data);
                        DisableTableFields(false);
                        ShowBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }

            //View Modal used to view each row [supplier credit details]
            $('body').on('click', '#view-supplier-credit', function(event) {
                let credit_id = $(this).data('id');
                event.preventDefault();
                viewSupplierCreditDetails(credit_id);
            });

            function viewSupplierCreditDetails(credit_id) {
                $.get("{{ route('supplier-credits.index') }}" + '/' + credit_id + '', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Credit details for supplier " + data.supplier + "");
                        $('#addSupplierCreditModal').modal('show');
                        populateStoreDetails(data);
                        DisableTableFields(true);
                        HideBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }

            function populateStoreDetails(data) {
                $('.credit_id').val(data.id);
                $('.supplier').val(data.supplier_id);
                $('.amount').val(data.amount);
                $('.date').val(data.date);
            }

            //this pops up confirm delete modal
            $('body').on('click', '#delete-supplier-credit', function(e) {
                let credit_id = $(this).data("id");
                e.preventDefault();
                $.get("{{ route('supplier-credits.index') }}" + '/' + credit_id + '', function(response) {
                    if (response.success) {

                        let data = response.data;
                        $('credit_id').val(data.id);
                        let action = data.is_deleted == 1 ? 'undelete' : 'delete';
                        $("#deleteSupplierCreditModal").modal('show');
                        $(".delete-alert-text").html(
                            `Are you sure you want to ${action} credit for supplier ${data.supplier}?`
                        );
                        $('.delete-ok-btn').on('click', function() {
                            deleteRecord(data.id);
                        });
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            });


            function deleteRecord(id) {
                let deleteUrl = '{{ route('supplier-credits.destroy', ':id') }}';
                deleteUrl = deleteUrl.replace(':id', id);
                $('.delete-ok-btn').html('Deleting...');
                $.ajax({
                    type: "DELETE",
                    url: deleteUrl,
                    success: function(response) {

                        let type = response.success ? 'success' : 'error';
                        let message = response.success || response.error;

                        if (response.success) {

                            let data = response.data;
                            $('.delete-ok-btn').html('Yes');
                            $('#deleteSupplierCreditModal').modal("hide");
                            $('.credit_id').val('');

                            resetTblInfo(data);
                            let tbl = $('#supplier-credits-table').DataTable();
                            tbl.ajax.reload();

                        }
                        displayResponse('.response', message, type);
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        displayResponse('.response', data.error, 'error');
                    }
                });
            }

            function DisableTableFields(bool) {
                $('.credit_id').attr('disabled', bool);
                $('.supplier').attr('disabled', bool);
                $('.amount').attr('disabled', bool);
                $('.date').attr('disabled', bool);
            }

            function HideBtns() {
                $('.addSupplierCreditBtn').hide();
                $('.clearBtn').hide();
            }

            function ShowBtns() {
                $('.addSupplierCreditBtn').show();
                $('.clearBtn').show();
            }


            function resetTblInfo(data) {
                if (data.no_of_credits && data.total_credits) {
                    let num = FormatNumber(data.no_of_credits);
                    let total = FormatNumber(data.total_credits);
                    $('.no_of_credits').html(num);
                    $('.total_credits').html(total);
                }
            }

            function validateForm() {

                let supplier = $('.supplier').val();
                let amount = $('.amount').val();
                let date = $('.date').val();

                let isValidForm = false;

                if (supplier.length < 1) {
                    displayResponse('.response', "Please select supplier", "error");
                } else if (amount.length < 1) {
                    displayResponse('.response', "Please enter amount", "error");
                } else if (date.length < 1) {
                    displayResponse('.response', "Please select date", "error");
                } else {
                    isValidForm = true;
                }

                return isValidForm;
            }

        });
    </script>
@endsection
