@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading d-flex align-items-center">
            <span class="response"></span>
            <h5 class="panel-title mb-0 text-dark">
                <i class="fa fa-home text-success"> /</i>
                <strong>Stores</strong>
                <span class="badge badge-info total_stores">
                    @isset($total_stores)
                        {{ number_format($total_stores) }}
                    @endisset
                </span>
            </h5>
            <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2" id="addNewStore">
                <i class="fa fa-plus-circle pr-1"></i>Add store</button>
        </div>

        <div class="panel-body">

            <div class="table table-sm table-responsive">
                <table class="table table-bordered table-hover stores-table" id="stores-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Store Name</th>
                            <th>is deleted</th>
                            <th>Added By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--Add stores -->
    <div class="modal fade nunito-font addStoreModal" id="addStoreModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <form name="stores" id="StoresForm">
                    @csrf
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new stores</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" class="form-control store_id  store_id" name="id"
                                placeholder="Enter stores id">
                        </div>

                        <div class="form-group">
                            <span><i class="text-danger pr-1">*</i>Store Name</span>
                            <input type="text" class="form-control name " name="name" placeholder="Enter stores name">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary rounded-pill addStoreBtn"
                                name="addStoreBtn">Save</button>
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
    <div class="modal fade" id="deleteStoresModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog" aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h6 class="modal-title delete-modal-title w-100 font-weight-bold">Delete stores</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger delete-alert-text">Are you sure you want to delete this stores
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
        const ajaxUrl = @json(route('stores.index.ajax'));
        const deletedSeletectedUrl = @json(route('selected-suppliers.remove'));
        const cat = 'stores';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            let table = $('#stores-table');
            let title = "List of stores in the system";
            let columns = [0, 1, 2, 3, 4];
            let dataColumns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
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
                $('.store_id').val('');
            });


            $('#addNewStore').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.addStoreBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.store_id').val('');
                $('#StoresForm').trigger("reset");
                $('#modalHeading').html("Register new stores");
                $('#addStoreModal').modal('show');
            });


            $('.addStoreBtn').click(function(e) {

                e.preventDefault();
                let store_id = $('.store_id').val();
                let method, url;

                if (store_id) {
                    url = "{{ route('stores.update', ':id') }}";
                    url = url.replace(":id", store_id);
                    method = 'PUT';
                } else {
                    url = "{{ route('stores.store') }}";
                    method = 'POST';
                }

                let isValidForm = validateForm();

                if (isValidForm) {
                    $(this).html('Sending..');

                    $.ajax({
                        data: $('#StoresForm').serialize(),
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(response) {

                            let message = response.success || response.error;
                            let type = response.success ? 'success' : 'error';

                            if (response.success) {
                                let data = response.data;
                                $('.store_id').val('');
                                $('#StoresForm').trigger("reset");
                                $('#addStoreModal').modal("hide");
                                resetTblInfo(data);
                                let tbl = $('#stores-table').DataTable();
                                tbl.ajax.reload();
                            }

                            displayResponse('.response', message, type);
                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            displayResponse('.response', data.error, 'error');
                            $('.addStoreBtn').html('Save Changes');
                        }
                    });
                }

            });

            //modal used to edit stores details [each row of the tbl]
            $('body').on('click', '#edit-store', function(event) {
                let store_id = $(this).data('id');
                event.preventDefault();
                editStore(store_id);
            });

            function editStore(store_id) {
                $('.store_id').val(store_id);
                $.get("{{ route('stores.index') }}" + '/' + store_id + '/edit', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Edit details of stores " + data.name + "");
                        $('.addStoreBtn').text("Update");
                        $('#addStoreModal').modal('show');
                        $('.store_id').val('');
                        populateStoreDetails(data);
                        DisableTableFields(false);
                        ShowBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }


            //View Modal used to view each row [stores details]
            $('body').on('click', '#view-store', function(event) {
                let store_id = $(this).data('id');
                event.preventDefault();
                viewStore(store_id);
            });

            function viewStore(store_id) {
                $.get("{{ route('stores.index') }}" + '/' + store_id + '', function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#modalHeading').html("Details of store " + data.name + "");
                        $('#addStoreModal').modal('show');
                        populateStoreDetails(data);
                        DisableTableFields(true);
                        HideBtns();
                    } else {
                        displayResponse('.response', response.error, 'error');
                    }
                });
            }

            function populateStoreDetails(data) {
                $('.store_id').val(data.id);
                $('.name').val(data.name);
            }

            //this pops up confirm delete modal
            $('body').on('click', '#delete-store', function(e) {
                let store_id = $(this).data("id");
                e.preventDefault();
                $.get("{{ route('stores.index') }}" + '/' + store_id + '', function(response) {
                    if (response.success) {

                        let data = response.data;
                        $('store_id').val(data.id);
                        let action = data.is_deleted == 1 ? 'undelete' : 'delete';
                        $("#deleteStoresModal").modal('show');
                        $(".delete-alert-text").html(
                            `Are you sure you want to ${action} stores ${data.name}?`
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
                let deleteUrl = '{{ route('stores.destroy', ':id') }}';
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
                            $('#deleteStoresModal').modal("hide");
                            $('.store_id').val('');

                            resetTblInfo(data);
                            let tbl = $('#stores-table').DataTable();
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
                $('.store_id').attr('disabled', bool);
                $('.name').attr('disabled', bool);
                $('.code').attr('disabled', bool);
            }

            function HideBtns() {
                $('.addStoreBtn').hide();
                $('.clearBtn').hide();
            }

            function ShowBtns() {
                $('.addStoreBtn').show();
                $('.clearBtn').show();
            }


            function resetTblInfo(data) {
                if (data.total) {
                    let total = FormatNumber(data.total);
                    $('.total_stores').html(total);
                }
            }

            function validateForm() {

                let name = $('.name').val();
                let isValidForm = false;

                if (name.length < 1) {
                    displayResponse('.response', "Please enter store name", "error");
                } else {
                    isValidForm = true;
                }

                return isValidForm;
            }

        });
    </script>
@endsection
