@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading d-flex justify-content-between align-items-center">
            <span class="response"></span>
            <div class="col-md-5">
                <h5 class="panel-title mb-0 text-dark">
                    <i class="fa fa-home text-success"> /</i>
                    <strong>Supplier Debts</strong>
                    <span class="badge badge-info no_of_debts">
                        @isset($no_of_debts)
                            {{ number_format($no_of_debts) }}
                        @endisset
                    </span>
                </h5>
            </div>

            <div class="col-md-4">
                <h5 class="panel-title mb-0 text-dark">
                    <strong>Total Debts: UGX.</strong>
                    @isset($total_debts)
                        <strong class="text-danger total_debts"> {{ number_format($total_debts) }}</strong>
                    @endisset
                </h5>
            </div>

            <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                id="addSupplierDebt">
                <i class="fa fa-plus-circle pr-1"></i>Add supplier debt</button>
        </div>

        <div class="panel-body">

            <div class="table table-sm table-responsive">
                <table class="table table-bordered table-hover supplier-debts-table" id="supplier-debts-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Debt Amount</th>
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

    <!--Add supplier debt -->
    <div class="modal fade nunito-font addSupplierDebtModal" id="addSupplierDebtModal" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true"
        role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form name="SupplierDebtsForm" id="SupplierDebtsForm">
                    @csrf
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new supplier debt</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" class="form-control debt_id  debt_id" name="id"
                                placeholder="Enter debt id">
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
                            <span><i class="text-danger pr-1">*</i> Debt Amount</span>
                            <input type="text" class="form-control amount" name="amount" placeholder="Enter amount">
                        </div>

                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span> Date</span>
                            <input type="date" class="form-control date " value="{{ date('Y-m-d') }}" name="date"
                                placeholder="Select date">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary rounded-pill addSupplierDebtBtn"
                                name="addSupplierDebtBtn">Save</button>
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


    <!--Modal delete supplier debt -->
    <div class="modal fade" id="deleteSupplierDebtModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog"
        aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h6 class="modal-title delete-modal-title w-100 font-weight-bold">Delete supplier debt</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger delete-alert-text">Are you sure you want to delete this supplier
                                debt
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
    </div> <!-- end of modal Delete Supplier Debt-->

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('suppliers.debts.ajax'));
        const deletedSeletectedUrl = @json(route('selected-suppliers.remove'));
        const cat = 'supplier_debts';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            let table = $('#supplier-debts-table');
            let title = "List of supplier debts in the system";
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
                $('.debt_id').val('');
            });

            Numberize('.amount');

            $('#addSupplierDebt').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.addSupplierDebtBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.debt_id').val('');
                $('#SupplierDebtsForm').trigger("reset");
                $('#modalHeading').html("Add new supplier debt");
                $('#addSupplierDebtModal').modal('show');
            });


            $('.addSupplierDebtBtn').click(function(e) {

                e.preventDefault();
                let debt_id = $('.debt_id').val();
                let method, url;

                if (debt_id) {
                    url = "{{ route('supplier-debts.update', ':id') }}";
                    url = url.replace(":id", debt_id);
                    method = 'PUT';
                } else {
                    url = "{{ route('supplier-debts.store') }}";
                    method = 'POST';
                }

                let isValidForm = validateForm();

                if (isValidForm) {
                    $(this).html('Sending..');

                    $.ajax({
                        data: $('#SupplierDebtsForm').serialize(),
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(response) {

                            let message = response.success || response.error;
                            let type = response.success ? 'success' : 'error';

                            if (response.success) {
                                let data = response.data;
                                $('.debt_id').val('');
                                $('#SupplierDebtsForm').trigger("reset");
                                $('#addSupplierDebtModal').modal("hide");
                                resetTblInfo(data);
                                let tbl = $('#supplier-debts-table').DataTable();
                                tbl.ajax.reload();
                            }

                            displayResponse('.response', message, type);
                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            displayResponse('.response', data.error, 'error');
                            $('.addSupplierDebtBtn').html('Save Changes');
                        }
                    });
                }

            });

            //modal used to edit supplier debt details [each row of the tbl]
            $('body').on('click', '#edit-supplier-debt', function(event) {
                let debt_id = $(this).data('id');
                event.preventDefault();
                editStore(debt_id);
            });

            function editStore(debt_id) {
                $('.debt_id').val(debt_id);
                $.get("{{ route('supplier-debts.index') }}" + '/' + debt_id + '/edit', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Edit debt details for supplier " + data.supplier + "");
                        $('.addSupplierDebtBtn').text("Update");
                        $('#addSupplierDebtModal').modal('show');
                        $('.debt_id').val('');
                        populateStoreDetails(data);
                        DisableTableFields(false);
                        ShowBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }


            //View Modal used to view each row [supplier debt details]
            $('body').on('click', '#view-supplier-debt', function(event) {
                let debt_id = $(this).data('id');
                event.preventDefault();
                viewSupplierDebtDetails(debt_id);
            });

            function viewSupplierDebtDetails(debt_id) {
                $.get("{{ route('supplier-debts.index') }}" + '/' + debt_id + '', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Debt details for supplier " + data.supplier + "");
                        $('#addSupplierDebtModal').modal('show');
                        populateStoreDetails(data);
                        DisableTableFields(true);
                        HideBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }

            function populateStoreDetails(data) {
                $('.debt_id').val(data.id);
                $('.supplier').val(data.supplier_id);
                $('.amount').val(data.amount);
                $('.date').val(data.date);
            }

            //this pops up confirm delete modal
            $('body').on('click', '#delete-supplier-debt', function(e) {
                let debt_id = $(this).data("id");
                e.preventDefault();
                $.get("{{ route('supplier-debts.index') }}" + '/' + debt_id + '', function(response) {
                    if (response.success) {

                        let data = response.data;
                        $('debt_id').val(data.id);
                        let action = data.is_deleted == 1 ? 'undelete' : 'delete';
                        $("#deleteSupplierDebtModal").modal('show');
                        $(".delete-alert-text").html(
                            `Are you sure you want to ${action} debt for supplier ${data.supplier}?`
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
                let deleteUrl = '{{ route('supplier-debts.destroy', ':id') }}';
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
                            $('#deleteSupplierDebtModal').modal("hide");
                            $('.debt_id').val('');

                            resetTblInfo(data);
                            let tbl = $('#supplier-debts-table').DataTable();
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
                $('.debt_id').attr('disabled', bool);
                $('.supplier').attr('disabled', bool);
                $('.amount').attr('disabled', bool);
                $('.date').attr('disabled', bool);
            }

            function HideBtns() {
                $('.addSupplierDebtBtn').hide();
                $('.clearBtn').hide();
            }

            function ShowBtns() {
                $('.addSupplierDebtBtn').show();
                $('.clearBtn').show();
            }


            function resetTblInfo(data) {
                if (data.no_of_debts && data.total_debts) {
                    let num = FormatNumber(data.no_of_debts);
                    let total = FormatNumber(data.total_debts);
                    $('.no_of_debts').html(num);
                    $('.total_debts').html(total);
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
