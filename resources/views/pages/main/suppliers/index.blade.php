@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-tile">
                <span class="pl-0 mt-4 response"></span>
                <div class="row nunito-font">
                    <div class="col-lg-2">
                        <h6 class="text-dark">
                            <i class="fa fa-home text-success"> /</i>
                            <strong>Suppliers</strong>
                            <span class="badge nunito-font  totl_suppliers">
                                @isset($number_of_suppliers)
                                    {{ number_format($number_of_suppliers) }}
                                @endisset
                            </span>
                        </h6>
                    </div>

                    <div class="col-lg-3">
                        <h5>
                            Credit: shs.<strong class="text-success totl_credit">
                                @isset($total_credit)
                                    {{ number_format($total_credit) }}
                                @endisset

                            </strong>
                        </h5>
                    </div>

                    <div class="col-lg-3">
                        <h5>
                            Debts: shs.<label class="text-danger totl_debt">
                                @isset($total_debts)
                                    {{ number_format($total_debts) }}
                                @endisset

                            </label>
                        </h5>
                    </div>

                    <div class="col-lg-2">
                        <h5>
                            <a class="text-info bolded" href="javascript:void(0)" id="createNewSupplier"> Add supplier</a>
                        </h5>
                    </div>

                    <div class="col-lg-2">
                        <div class="btn-group">
                            <button type="button"
                                class="btn border-info text-success bolded form-control text-center dropdown-toggle downloadfilebtn"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                @can('isAdmin')
                                    <li><a href="" class="add-link text-dark text-decoration-none" data-toggle="modal"
                                            data-target="#importSuppliers"><strong>Import suppliers</strong>
                                        </a></li>

                                    <li>
                                        <a class="text-decoration-none text-dark
                nunito-font"
                                            href="javascript:void(0)" id="removeAllSuppliers"> Delete all suppliers</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="panel-body">

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

            <div class="table table-sm table-responsive custom-family">

                <table class="table table-bordered table-hover suppliers-table" id="suppliers-table">

                    <thead>
                        <tr>
                            <th></th>
                            {{-- <th class="td-sm">No</th> --}}
                            <th>Supplier</th>
                            <th>Mobile No</th>
                            {{-- <th>Address</th> --}}
                            {{-- <th>Email</th> --}}
                            <th>Credit</th>
                            <th>Debt</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>


            </div>
        </div>
    </div>



    <!--Add suppliers -->
    <div class="modal fade nunito-font addSuppliersModal" id="addSuppliersModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <form name="suppliers" id="SuppliersForm">
                    @csrf
                    <div class="modal-header text-center">
                        <h5 class="modal-title w-100 font-weight-bold" id="modalHeading">Add new supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            {{-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> --}}
                            <input type="hidden" class="form-control supplierId  supplierId" name="id"
                                placeholder="Enter supplier id" Required autofocus>
                        </div>

                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span>Name</span>
                            <input type="text" class="form-control name " name="name"
                                placeholder="Enter supplier name" Required autofocus>
                        </div>

                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span>Address</span>
                            <input type="text" class="form-control address " name="address" placeholder="Enter address"
                                Required autofocus>
                        </div>


                        <div class="form-group">
                            <span><span class="text-danger pr-2">*</span>Contact</span>
                            <input type="text" class="form-control contact " name="contact" placeholder="Enter contact"
                                Required autofocus>
                        </div>


                        <div class="form-group">
                            <span>Email</span>
                            <input type="email" class="form-control email " name="email" placeholder="Email (optional)">
                        </div>


                        <div class="form-group">
                            <span>Debt</span>
                            <input type="text" class="form-control debt " name="debt" placeholder="Enter debt">
                        </div>


                        <div class="form-group">
                            <span>Credit</span>
                            <input type="text" class="form-control credit " name="credit"
                                placeholder="Enter credit">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success addsupplierBtn"
                                name="AddsupplierBtn">Save</button>
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

    <!--Import Suppliers -->
    <div class="modal fade nunito-font" id="importSuppliers" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="{{ Route('suppliers.import') }}" method="post" enctype="multipart/form-data"
                    name="inportExpensesForm">
                    @csrf

                    <div class="modal-header text-center">
                        <h5 class="modal-title w-100 font-weight-bold">
                            Import an excel file of suppliers </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <span>Select file for Upload</span>
                        </div>

                        <div class="form-group">
                            <input type="file" class="form-control-file @error('select_file') is-invalid @enderror"
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


    <!--Modal Deletesuppliers -->
    <div class="modal fade" id="deleteSuppliersModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title delete-modal-title w-100 font-weight-bold">Delete supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger delete-alert-text">Are you sure you want to delete this supplier
                                <small class="text-dark text-muted bolded">
                                </small>
                                ?

                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success delete-ok-btn" name="ConfirmBtn">Yes</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end of modal Deletesuppliers-->


    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script> --}}
    <script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('suppliers.home'));
        const deletedSeletectedUrl = @json(route('selected-suppliers.remove'));
        const cat = 'supplier';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {


            //code that displays results of the table index()
            let table = $('#suppliers-table');
            let title = "List of registered suppliers in the system";
            let columns = [1, 2, 3, 4];
            let dataColumns = [{
                    data: 'checkbox',
                    name: 'checkbox'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'credit',
                    name: 'credit'
                },
                {
                    data: 'debt',
                    name: 'debt'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ];

            makeDataTable(table, title, columns, dataColumns);

            $('#createNewSupplier').click(function(e) {
                e.preventDefault();
                DisableTableFields(false);
                ShowBtns();
                $('.addsupplierBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('.supplierId').val('');
                $('#SuppliersForm').trigger("reset");
                $('#modalHeading').html("Register new supplier");
                $('#addSuppliersModal').modal('show');
            });


            Numberize(".debt");
            Numberize(".credit");

            function Numberize(i) {
                $(document).on("keyup", i, function() {
                    if (this.value.length > 0) {
                        let n = parseInt(this.value.replace(/\D/g, ''), 10);
                        $(this).val(n.toLocaleString());
                    }
                });
            }

            //modal used to edit suppliers details [each row of the tbl]
            $('body').on('click', '#edit-supplier', function(event) {
                let supplier_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('suppliers.index') }}" + '/' + supplier_id + '/edit', function(data) {

                    $('#modalHeading').html("Edit details of supplier " + data.name + "");
                    $('.addsupplierBtn').text("Edit supplier");
                    $('#addSuppliersModal').modal('show');
                    $('.supplierId').val(data.id);
                    $('.name').val(data.name);
                    $('.address').val(data.address);
                    $('.contact').val(data.contact);
                    $('.email').val(data.email);
                    $('.debt').val(data.debt);
                    $('.credit').val(data.credit);
                    DisableTableFields(false);
                    ShowBtns();
                })
            });


            //View Modal used to view each row [suppliers details]
            $('body').on('click', '#view-supplier', function(event) {
                let supplier_id = $(this).data('id');
                event.preventDefault();

                $.get("{{ route('suppliers.index') }}" + '/' + supplier_id + '', function(data) {

                    $('#modalHeading').html("Details of supplier " + data.name + "");
                    $('#addSuppliersModal').modal('show');
                    $('.supplierId').val(data.id);
                    $('.name').val(data.name);
                    $('.address').val(data.address);
                    $('.contact').val(data.contact);
                    $('.email').val(data.email);
                    $('.debt').val(data.debt);
                    $('.credit').val(data.credit);
                    DisableTableFields(true);
                    HideBtns();
                })
            });


            $('.addSupplierBtn').click(function(e) {

                e.preventDefault();

                let Errors = validateForm();
                if (Errors.length == 0) {
                    $(this).html('Sending..');

                    $.ajax({
                        data: $('#SuppliersForm').serialize(),
                        url: "{{ route('suppliers.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {

                            let message = response.success || response.error;
                            let type = response.success ? 'success' : 'error';

                            if (response.success) {
                                $('#SuppliersForm').trigger("reset");
                                $('#addSuppliersModal').modal("hide");

                                ResetTblInfo(response.data);
                                let tbl = $('#suppliers-table').DataTable();
                                tbl.ajax.reload();
                            }

                            ShowResponse('.response', message, type);

                        },
                        error: function(data) {
                            console.log('Error:', data.error);
                            ShowResponse('.response', data.error, 'error');
                            $('.addsupplierBtn').html('Save Changes');
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

            //this pops up confirm delete modal
            $('body').on('click', '#delete-supplier', function(e) {
                let supplier_id = $(this).data("id");
                e.preventDefault();
                $("#deleteSuppliersModal").modal('show');
                $(".delete-alert-text").html("Are you sure you want to delete this supplier?");
                $('.delete-ok-btn').on('click', function() {
                    ListenAndDoDeletion(supplier_id);
                });

            });


            function ListenAndDoDeletion(id) {
                let deleteUrl = '{{ route('suppliers.destroy', ':id') }}';
                deleteUrl = deleteUrl.replace(':id', id);
                $('.delete-ok-btn').html('Deleting...');
                $.ajax({
                    type: "DELETE",
                    url: deleteUrl,
                    success: function(data) {
                        let resp = data.success;
                        $('.delete-ok-btn').html('Yes');
                        $('#deleteSuppliersModal').modal("hide");
                        ShowResponse('.response', resp, 'success');
                        ResetTblInfo(data);
                        let tbl = $('#suppliers-table').DataTable();
                        tbl.ajax.reload();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        ShowResponse('.response', data.error, 'error');
                    }
                });
            }


            function DisableTableFields(bool) {

                $('.supplierId').attr('disabled', bool);
                $('.name').attr('disabled', bool);
                $('.address').attr('disabled', bool);
                $('.contact').attr('disabled', bool);
                $('.email').attr('disabled', bool);
                $('.debt').attr('disabled', bool);
                $('.credit').attr('disabled', bool);
            }

            function HideBtns() {
                $('.addsupplierBtn').hide();
                $('.clearBtn').hide();
                $('.closeBtn').hide();
            }

            function ShowBtns() {
                $('.addsupplierBtn').show();
                $('.clearBtn').show();
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

            function ResetTblInfo(data) {
                let totl_number, sum_of_credits, sum_of_debts;
                totl_number = FormatNumber(data.totl_no);
                sum_of_credits = FormatNumber(data.totl_credit);
                sum_of_debts = FormatNumber(data.totl_debt);

                $('.totl_suppliers').html(totl_number);
                $('.totl_credit').html(sum_of_credits);
                $('.totl_debt').html(sum_of_debts);
            }

            function validateForm() {
                let name = $('.name').val();
                let address = $('.address').val();
                let contact = $('.contact').val();
                let errors = [];
                if (name.length < 1) {
                    let nameErr = "Please enter the name of the supplier";
                    errors.push(nameErr);
                }
                if (address.length < 1) {
                    let addressErr = "Please enter the address of the supplier";
                    errors.push(addressErr);
                }
                if (contact.length < 1) {
                    let contactErr = "Please enter supplier's contact";
                    errors.push(contactErr);
                }
                return errors;
            }



            $("#removeAllSuppliers").bind("click", function() {
                RemoveAllSuppliers();
            });

            function RemoveAllSuppliers() {
                $.confirm({
                    boxWidth: '30%',
                    icon: 'fa fa-warning',
                    theme: 'light',
                    closeIcon: true,
                    draggable: true,
                    closeIconClass: 'fa fa-close text-danger',
                    title: 'Delete all suppliers',
                    content: 'Are you sure you want to remove all suppliers',
                    buttons: {
                        confirm: function() {
                            let self = this;
                            return $.ajax({
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                },
                                url: '{{ Route('suppliers.truncate') }}',
                                type: 'POST',
                                // dataType: 'json',
                            }).done(function(data) {

                                $.alert({
                                    title: 'Message',
                                    content: data.success,
                                });
                                $(".totl_suppliers").text(data.totl_no);
                                $(".totl_credit").text(data.totl_credit);
                                $(".totl_debt").text(data.totl_debt);
                                let tbl = $('#suppliers-table').DataTable();
                                tbl.ajax.reload();


                            }).fail(function(data) {
                                $.alert({
                                    title: 'Response',
                                    content: "Suppliers not deleted:" + data.fail,
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
