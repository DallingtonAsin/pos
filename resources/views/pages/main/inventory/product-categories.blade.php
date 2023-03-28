@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title d-flex align-items-center">
                <span class="pl-0 mt-4 response"></span>
                <div class="col-lg-9 text-dark">
                    <h6 class="panel-title">
                        <i class="fa fa-home text-success"> /</i>
                        <strong>Recorded Item categories</strong>
                        <span class="badge totl-PdtCategory">{{ $no_of_categories }}</span>
                    </h6>
                </div>

                <div class="col-lg-3">
                    <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                        id="createNewPdtCategory"><i class="fa fa-plus-circle pr-1"></i>Add stock category</button>

                    <a href="" class="btn btn-default btn-sm outline-none rounded-pill ml-auto mb-2"
                        data-toggle="modal" data-target="#importCategories"><strong>Import categories</strong></a>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="col-lg-10 nunito-font text-center">

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


            <div class="table table-responsive custom-family">
                <table class="table table-bordered product-categories-table" id="product-categories-table">
                    <thead>
                        <tr class="text-center">
                            <th style="width:10%"></th>
                            {{-- <th style="width:20%">No</th> --}}
                            <th style="width:50%">Item Category</th>
                            <th style="width:20%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>

            <!--Modal DeleteItemCategory -->
            <div class="modal fade" id="deletePdtCategoryModal" tabindex="-1" role="dialog"
                aria-labelledby="deleteModalLabel">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h5 class="modal-title w-100 font-weight-bold">Delete Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="form-group">
                                <div class="text-center">
                                    <label class="text-danger">Are you sure you want
                                        to delete item category
                                        <small class="text-dark text-muted bolded">
                                        </small>
                                        ?

                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success delete-ok-btn" id="delete-ok-btn"
                                    name="ConfirmBtn">Yes</button>
                                <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end of modal DeleteItemCategory-->

            <!--Add product category -->
            <div class="modal fade nunito-font" id="addItemCategoryModal" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <form name="categories" id="PdtCategoryForm">
                            @csrf
                            <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Add item category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <span><span class="text-danger pr-2">*</span>Item Category</span>
                                    <input type="hidden" name="id" class="PdtCategoryId">
                                    <input type="text" class="form-control PdtCategory " name="item-category"
                                        placeholder="Enter item category" Required autofocus>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success addPdtCategoryBtn"
                                        name="AddCategoryBtn">Save</button>
                                    <button type="reset" class="btn btn-danger clearBtn">Clear</button>

                                </div>

                                <div class="form-group">
                                    <span class="errors-section text-danger nunito-font"></span>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!--Import PdtCategory Categories -->
            <div class="modal fade nunito-font" id="importCategories" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <form action="{{ Route('categories.import') }}" method="post" enctype="multipart/form-data"
                            name="importCategoriesForm">
                            @csrf

                            <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold">
                                    Import an excel file of product category categories </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <span>Select file for Upload</span>
                                </div>

                                <div class="form-group">
                                    <input type="file"
                                        class="form-control-file @error('select_file') is-invalid @enderror"
                                        name="select_file" Required autofocus>
                                </div>

                                @error('select_file')
                                    <div class='alert alert-danger alert-dismissible text-center' role='alert'>
                                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                            <span aria-hidden='true'>&times;</span></button>
                                        <strong>Sorry!</strong> {{ $message }}
                                    </div>
                                @enderror

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Upload</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
        const ajaxUrl = @json(route('get-stockItems'));
        const deletedSeletectedUrl = @json(route('selected-stockcats.remove'));
        const cat = 'stockcats';
        const token = "{{ csrf_token() }}";
    </script>
    <script>
        //code that displays results of the table index()
        var table = $('#product-categories-table');
        var title = "List of recorded item categories in the system";
        var columns = [0, 1];
        var dataColumns = [{
                data: 'checkbox',
                name: 'checkbox'
            },
            //  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,  searchable: false },
            // {data: 'id', name:'id'},
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ];

        makeDataTable(table, title, columns, dataColumns);
    </script>



    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            onClickSubmitBtn();

            $('#createNewPdtCategory').click(function(e) {
                e.preventDefault();
                NullifyFields();
                ShowHideBtns('show');
                $('.addPdtCategoryBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('#PdtCategoryForm').trigger("reset");
                $('#modalHeading').html("Add new stock category");
                DisableFormFields(false);
                $('#addItemCategoryModal').modal('show');

            });

            //modal used to edit PdtCategory details [each row of the tbl]
            $('body').on('click', '#edit-pdt-category', function(event) {
                var PdtCategory_id = $(this).data('id');
                event.preventDefault();

                ShowHideBtns('show');
                $('.addPdtCategoryBtn').text("Update");
                $('#addItemCategoryModal').modal('show');
                var Url = "{{ route('product-categories.show', ':id') }}";
                Url = Url.replace(':id', PdtCategory_id);
                $.ajax({

                    url: Url,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {

                        $('#modalHeading').html("Edit details of product category item " + data
                            .name + "");
                        $('.PdtCategoryId').val(data.id);
                        $('.PdtCategory').val(data.name);
                        DisableFormFields(false);
                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                    }
                });

            });

            function UpdatePdtCategory(PdtCategory_id) {

                $('.errors-section').html('');
                $('.addPdtCategoryBtn').html('Updating item...');

                var Url = "{{ route('product-categories.update', ':id') }}";
                Url = Url.replace(':id', PdtCategory_id);
                $.ajax({
                    data: $('#PdtCategoryForm').serialize(),
                    url: Url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {

                        $('#PdtCategoryForm').trigger("reset");
                        $('#addItemCategoryModal').modal("hide");
                        var resp = data.success;
                        ShowResponse('.response', resp, 'success');
                        ResetTblInfo(data);
                        var tbl = $('#product-categories-table').DataTable();
                        tbl.ajax.reload();

                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                        $('.addPdtCategoryBtn').html('Save Changes');
                    }
                });

            }

            function recordPdtCategory() {

                $('.errors-section').html('');
                $('.addPdtCategoryBtn').html('Sending data..');

                $.ajax({
                    data: $('#PdtCategoryForm').serialize(),
                    url: "{{ route('product-categories.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {

                        $('#PdtCategoryForm').trigger("reset");
                        $('#addItemCategoryModal').modal("hide");
                        var resp = data.success;
                        ShowResponse('.response', resp, 'success');
                        ResetTblInfo(data);
                        var tbl = $('#product-categories-table').DataTable();
                        tbl.ajax.reload();

                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                        $('.addPdtCategoryBtn').html('Save Changes');
                    }
                });

            }


            //View Modal used to view each row [PdtCategory details]
            $('body').on('click', '#view-pdt-category', function(event) {
                var PdtCategory_id = $(this).data('id');
                event.preventDefault();
                ShowHideBtns('hide');
                $.get("{{ route('product-categories.index') }}" + '/' + PdtCategory_id + '', function(
                data) {
                    var bprice = data.buying_price;
                    var sprice = data.selling_price;
                    $('#modalHeading').html("Details of product category " + data.name + "");
                    $('#addItemCategoryModal').modal('show');
                    $('.PdtCategoryId').val(data.id);
                    $('.PdtCategory').val(data.name);
                    DisableFormFields(true);

                })
            });


            function onClickSubmitBtn() {
                $('.addPdtCategoryBtn').click(function(e) {
                    var id = $(".PdtCategoryId").val();
                    e.preventDefault();
                    var Errors = validateForm();
                    if (Errors.length == 0) {
                        if (id) {
                            UpdatePdtCategory(id);

                        } else {
                            recordPdtCategory();
                        }

                    } else {
                        var i;
                        var message = "";
                        for (i = 0; i < Errors.length; i++) {
                            message += Errors[i] + "<br>";
                        }
                        //ShowResponse('.errors-section', resp, 'error');
                        $('.errors-section').html(message);

                    }

                });
            }


            //this pops up confirm delete modal
            $('body').on('click', '#delete-pdt-category', function(e) {
                var PdtCategory_id = $(this).data("id");
                e.preventDefault();
                $("#deletePdtCategoryModal").modal('show');
                $('.delete-ok-btn').on('click', function() {
                    ListenAndDoDeletion(PdtCategory_id);
                });

            });


            function ListenAndDoDeletion(id) {
                var deleteUrl = '{{ route('product-categories.destroy', ':id') }}';
                deleteUrl = deleteUrl.replace(':id', id);
                $('.delete-ok-btn').html('Deleting...');
                $.ajax({
                    type: "DELETE",
                    url: deleteUrl,
                    success: function(data) {
                        var resp = data.success;
                        $('.delete-ok-btn').html('Yes');
                        $('#deletePdtCategoryModal').modal("hide");
                        ShowResponse('.response', resp, 'success');
                        ResetTblInfo(data);
                        var tbl = $('#product-categories-table').DataTable();
                        tbl.ajax.reload();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        ShowResponse('.response', data.error, 'error');
                    }
                });
            }

            function NullifyFields() {
                $('.PdtCategoryId').val('');
                $('.item_id').val('');
                $('.item-name').val('');
                $('.category').val('');
                $('#supplier').val('');
                $('.quantity').val('');
                $('.expiry_date').val('');
                $('.original_price').val('');
                $('.selling_price').val('');
            }



            function DisableFormFields(bool) {

                $('.PdtCategory').attr('disabled', bool);
            }

            function ShowHideBtns(action) {

                if (action == 'hide') {
                    $('.addPdtCategoryBtn').hide();
                    $('.clearBtn').hide();
                    $('.closeBtn').hide();
                } else if (action == 'show') {
                    $('.addPdtCategoryBtn').show();
                    $('.clearBtn').show();
                    $('.closeBtn').show();
                }
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
                var totl_Of_PdtCategories;
                totl_Of_PdtCategories = FormatNumber(response.totl_no);
                $('.totl-PdtCategory').html(totl_Of_PdtCategories);
            }

            function validateForm() {
                var item = $('.PdtCategory').val();

                var errors = [];
                if (item.length < 1) {
                    var itemCatNameErr = "Please enter the name of item category";
                    errors.push(itemCatNameErr);
                }

                return errors;

            }


            $("#removeAllStockCats").bind("click", function() {
                RemoveAllStockCategories();
            });

            function RemoveAllStockCategories() {
                $.confirm({
                    boxWidth: '30%',
                    icon: 'fa fa-warning',
                    theme: 'light',
                    closeIcon: true,
                    draggable: true,
                    closeIconClass: 'fa fa-close text-danger',
                    title: 'Delete all stock categories',
                    content: 'Are you sure you want to remove all stock categories',
                    buttons: {
                        confirm: function() {
                            var self = this;
                            return $.ajax({
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                },
                                url: '{{ Route('categories.truncate') }}',
                                type: 'POST',
                            }).done(function(data) {

                                $.alert({
                                    title: 'Message',
                                    content: data.success,
                                });
                                $(".totl-PdtCategory").text(data.totl_no);
                                var tbl = $('#product-categories-table').DataTable();
                                tbl.ajax.reload();

                            }).fail(function(data) {
                                $.alert({
                                    title: 'Response',
                                    content: "Categories of stock not deleted:" + data
                                        .fail,
                                });
                                console.log(data);

                            });

                        },
                        cancel: function() {

                        }
                    },
                });

            }








        });
    </script>
@endsection
