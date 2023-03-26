@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading row d-flex justify-content-between align-items-center">
            <span class="response"></span>
            <div class="col-md-5">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fa fa-home text-success"> /</i>
                    <strong>Taken Bottles</strong>
                    <span class="badge badge-info no_of_bottles">
                        @isset($no_of_bottles)
                            {{ number_format($no_of_bottles) }}
                        @endisset
                    </span>
                </h5>
            </div>

    

            <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                id="addTakenBottle">
                <i class="fa fa-plus-circle pr-1"></i>Add taken bottle</button>
        </div>

        <div class="panel-body">

            <div class="table table-sm table-responsive">
                <table class="table table-bordered table-hover taken-bottles-table" id="taken-bottles-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Bottle</th>
                            <th>Quantity</th>
                            <th>Taken on</th>
                            <th>is Deleted</th>
                            <th>is Returned</th>
                            <th>Returned On</th>
                            <th>Added By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--Add taken bottle -->
    <div class="modal fade nunito-font addTakenBottleModal" id="addTakenBottleModal" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true"
        role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form name="TakenBottleForm" id="TakenBottleForm">
                    @csrf
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new taken bottle</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" class="form-control taken_bottle_record_id  taken_bottle_record_id" name="id"
                                placeholder="Enter credit id">
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i> Customer</span>
                            <select class="form-control customer" name="customer">
                                <option value="">Select customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i> Bottle Name</span>
                            <select class="form-control bottle" name="bottle">
                                <option value="">Select bottle</option>
                                @foreach ($stock as $item)
                                    <option value="{{ $item->id }}">{{ $item->item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i> Quantity</span>
                            <input type="text" class="form-control quantity" name="quantity" placeholder="Enter quantity">
                        </div>

                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span>Date</span>
                            <input type="date" class="form-control taken_on" value="{{ date('Y-m-d') }}" name="taken_on"
                                placeholder="Select date">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary rounded-pill addTakenBottleBtn"
                                name="addTakenBottleBtn">Save</button>
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
    <div class="modal fade" id="deleteTakenBottleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog"
        aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h6 class="modal-title delete-modal-title w-100 font-weight-bold">Delete taken bottle</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger delete-alert-text">Are you sure you want to delete this taken bottle
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
    </div> <!-- end of modal Delete Taken Bottle-->

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('taken-bottles.index.ajax'));
        const cat = 'taken_bottles';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            let table = $('#taken-bottles-table');
            let title = "List of taken bottles in the system";
            let columns = [0, 1, 2, 3, 4];
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
                    data: 'bottle',
                    name: 'bottle'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'taken_on',
                    name: 'taken_on'
                },
                {
                    data: 'is_deleted',
                    name: 'is_deleted'
                },
                {
                    data: 'is_returned',
                    name: 'is_returned'
                },
                {
                    data: 'returned_on',
                    name: 'returned_on'
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
                $('.taken_bottle_record_id').val('');
            });

            Numberize('.amount');

            $('#addTakenBottle').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.addTakenBottleBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.taken_bottle_record_id').val('');
                $('#TakenBottleForm').trigger("reset");
                $('#modalHeading').html("Add new taken bottle");
                $('#addTakenBottleModal').modal('show');
            });


            $('.addTakenBottleBtn').click(function(e) {

                e.preventDefault();
                let taken_bottle_record_id = $('.taken_bottle_record_id').val();
                let method, url;

                if (taken_bottle_record_id) {
                    url = "{{ route('taken-bottles.update', ':id') }}";
                    url = url.replace(":id", taken_bottle_record_id);
                    method = 'PUT';
                } else {
                    url = "{{ route('taken-bottles.store') }}";
                    method = 'POST';
                }

                let isValidForm = validateForm();

                if (isValidForm) {
                    $(this).html('Sending..');

                    $.ajax({
                        data: $('#TakenBottleForm').serialize(),
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(response) {

                            let message = response.success || response.error;
                            let type = response.success ? 'success' : 'error';

                            if (response.success) {
                                let data = response.data;
                                $('.taken_bottle_record_id').val('');
                                $('#TakenBottleForm').trigger("reset");
                                $('#addTakenBottleModal').modal("hide");
                                resetTblInfo(data);
                                let tbl = $('#taken-bottles-table').DataTable();
                                tbl.ajax.reload();
                            }

                            displayResponse('.response', message, type);
                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            displayResponse('.response', data.error, 'error');
                            $('.addTakenBottleBtn').html('Save Changes');
                        }
                    });
                }

            });

            //modal used to edit taken bottle details [each row of the tbl]
            $('body').on('click', '#edit-taken-bottle', function(event) {
                let taken_bottle_record_id = $(this).data('id');
                event.preventDefault();
                editTakenBottle(taken_bottle_record_id);
            });

            function editTakenBottle(taken_bottle_record_id) {
                $('.taken_bottle_record_id').val(taken_bottle_record_id);
                $.get("{{ route('taken-bottles.index') }}" + '/' + taken_bottle_record_id + '/edit', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Edit details of taken bottle by customer " + data.customer + "");
                        $('.addTakenBottleBtn').text("Update");
                        $('#addTakenBottleModal').modal('show');
                        $('.taken_bottle_record_id').val('');
                        populateTakenBottleDetails(data);
                        DisableTableFields(false);
                        ShowBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }


            //View Modal used to view each row [taken bottle details]
            $('body').on('click', '#view-taken-bottle', function(event) {
                let taken_bottle_record_id = $(this).data('id');
                event.preventDefault();
                viewDetails(taken_bottle_record_id);
            });

            function viewDetails(taken_bottle_record_id) {
                $.get("{{ route('taken-bottles.index') }}" + '/' + taken_bottle_record_id + '', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Details of taken bottle by customer " + data.customer + "");
                        $('#addTakenBottleModal').modal('show');
                        populateTakenBottleDetails(data);
                        DisableTableFields(true);
                        HideBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }

            function populateTakenBottleDetails(data) {
                $('.taken_bottle_record_id').val(data.id);
                $('.customer').val(data.customer_id);
                $('.bottle').val(data.bottle_id);
                $('.quantity').val(data.quantity);
                $('.taken_on').val(data.taken_on);
            }

            //this pops up confirm delete modal
            $('body').on('click', '#delete-taken-bottle', function(e) {
                let taken_bottle_record_id = $(this).data("id");
                e.preventDefault();
                $.get("{{ route('taken-bottles.index') }}" + '/' + taken_bottle_record_id + '', function(response) {
                    if (response.success) {

                        let data = response.data;
                        $('taken_bottle_record_id').val(data.id);
                        let action = data.is_deleted == 1 ? 'undelete' : 'delete';
                        $("#deleteTakenBottleModal").modal('show');
                        $(".delete-alert-text").html(
                            `Are you sure you want to ${action} taken bottle by customer ${data.customer}?`
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
                let deleteUrl = '{{ route('taken-bottles.destroy', ':id') }}';
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
                            $('#deleteTakenBottleModal').modal("hide");
                            $('.taken_bottle_record_id').val('');

                            resetTblInfo(data);
                            let tbl = $('#taken-bottles-table').DataTable();
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
                $('.taken_bottle_record_id').attr('disabled', bool);
                $('.customer').attr('disabled', bool);
                $('.bottle').attr('disabled', bool);
                $('.quantity').attr('disabled', bool);
                $('.taken_on').attr('disabled', bool);
            }

            function HideBtns() {
                $('.addTakenBottleBtn').hide();
                $('.clearBtn').hide();
            }

            function ShowBtns() {
                $('.addTakenBottleBtn').show();
                $('.clearBtn').show();
            }


            function resetTblInfo(data) {
                if (data.no_of_bottles) {
                    let num = FormatNumber(data.no_of_bottles);
                    $('.no_of_bottles').html(num);
                }
            }

            function validateForm() {

                let customer = $('.customer').val();
                let bottle = $('.bottle').val();
                let quantity = $('.quantity').val();
                let taken_on = $('.taken_on').val();

                let isValidForm = false;

                if (customer.length < 1) {
                    displayResponse('.response', "Please select customer", "error");
                } else if (bottle.length < 1) {
                    displayResponse('.response', "Please select bottle from stock", "error");
                } else if (quantity.length < 1) {
                    displayResponse('.response', "Please enter quantity", "error");
                }else if (taken_on.length < 1) {
                    displayResponse('.response', "Please select date", "error");
                } else {
                    isValidForm = true;
                }

                return isValidForm;
            }

        });
    </script>
@endsection