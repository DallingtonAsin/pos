@extends('layouts.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading d-flex align-items-center">
                <span class="pl-0 mt-4 response"></span>
                    <div class="col-lg-4 text-dark">
                        <h5 class="panel-title">
                            <i class="fa fa-home text-success"> /</i>
                            <strong>Purchases</strong>
                            <span class="badge nunito-font totl-no">
                                @isset($no_of_purchases)
                                    {{ number_format($no_of_purchases) }}
                                @endisset

                            </span>
                        </h5>
                    </div>

                    @can('isAdmin')
                        <div class="col-lg-5">
                            <span>
                                <h5 class="panel-title">
                                    <strong>Total cost:</strong>
                                    <span class="text-success text-center">UGX.
                                        <strong class="purchase-value totl-purchases">
                                            @isset($totl_cost_of_purchases)
                                                {{ number_format($totl_cost_of_purchases) }}
                                            @endisset
                                        </strong>
                                    </span>
                                </h5>
                            </span>
                        </div>
                    @endcan

                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-sm outline-none rounded-pill ml-auto mb-2"
                            id="createNewpurchase"><i class="fa fa-plus-circle pr-1"></i>Add purchase</button>

                        <a href="" class="btn btn-default btn-sm outline-none rounded-pill ml-auto mb-2"
                            data-toggle="modal" data-target="#importPurchases"><strong>Import purchases</strong></a>
                    </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-lg-10 text-center nunito-font">
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
                <table class="table table-sm  table-bordered table-hover purchase-table" id="purchase-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Item</th>
                            <th>Item Code</th>
                            <th>Qty</th>
                            <th>C.Price</th>
                            <th>T.Cost</th>
                            <th>Purchase Date</th>
                            <th>Supplier</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>




    <!--Add new purchase -->
    <div class="modal fade nunito-font" id="addPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <form name="purchaseForm" id="purchaseForm">
                    @csrf

                    <div class="modal-header text-center">
                        <h5 class="modal-title w-100 font-weight-bold custom-family" id="modalHeading">
                            Add new purchase item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">


                        <div class="form-group">
                            <span><span class="text-danger">*</span>Stock Item</span>
                            <select class="form-control item-name" name="item">
                                <option value="">Select stock item</option>
                                @foreach ($stock as $item)
                                    <option value="{{ $item->id }}">{{ $item->item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <span>Item Code</span>
                            <input type="text" class="form-control item_code" name="item_code" readonly>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <span>Serial Number</span>
                                <input type="hidden" class="purchaseId" name="id">
                                <input type="text" class="form-control serial_no" name="serial_no"
                                    placeholder="Enter serial number of the purchased item">
                            </div>

                            <div class="col-md-6">
                                <span>Receipt Number</span>
                                <input type="text" class="form-control  receipt_no" name="receipt_no"
                                    placeholder="Enter receipt number of the purchased item">
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-md-6">
                                <span><span class="text-danger">*</span> Quantity</span>
                                <input type="text" class="form-control quantity" id="qty" name="quantity"
                                    placeholder="Enter Quantity" required autofocus>
                            </div>

                            <div class="col-md-6">
                                <span><span class="text-danger">*</span>Cost Price Per Item</span>
                                <input type="text" class="form-control cost_price" name="cost_price"
                                    placeholder="Enter cost price per item" required autofocus>
                            </div>

                            <div class="col-md-4">

                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <span><span class="text-danger">*</span> Retail price</span>
                                <input type="text" class="form-control retail_price" name="retail_price"
                                    placeholder="Enter retail price" required autofocus>
                            </div>

                            <div class="col-md-6">
                                <span>Wholesale price</span>
                                <input type="text" class="form-control wholesale_price" name="wholesale_price"
                                    placeholder="Enter wholesale price">
                            </div>
                        </div>


                        <div class="form-group">
                            <span><span class="text-danger">*</span> Supplier</span>
                            <select class="form-control supplier" id="supplier" name="supplier">
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <span>Date of purchase</span>
                            <input type="date" class="form-control date_of_purchase" name="date_of_purchase"
                                value="{{ date('Y-m-d') }}" placeholder="Enter date of purchase">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success addPurchaseBtn" name="AddItemBtn">Save</button>
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

    <!--Import Purchases -->
    <div class="modal fade nunito-font" id="importPurchases" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="{{ Route('purchases.import') }}" method="post" enctype="multipart/form-data"
                    name="inportPurchasesForm">
                    @csrf

                    <div class="modal-header text-center">
                        <h5 class="modal-title w-100 font-weight-bold custom-family">
                            Import an excel file of purchased items</h5>
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
                                name="select_file" required autofocus>
                        </div>

                        @error('select_file')
                            <div class='alert alert-danger alert-dismissible text-center' role='alert'>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span></button>
                                <strong>Sorry!</strong> {{ $message }}
                            </div>
                        @enderror

                        <div class="form-group">
                            <button type="submit" class="btn btn-success" name="AddItemBtn">Upload</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Modal Deletepurchase -->
    <div class="modal fade" id="deletepurchaseModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title w-100 font-weight-bold">Delete Purchased Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="text-center">
                            <label class="text-danger">Are you sure you want to delete purchase
                                <small class="text-dark text-muted bolded purchase-to-delete">
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
    </div>
    <!-- end of modal Deletepurchase-->


    <script src="{{ asset('vendors/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const ajaxUrl = @json(route('get-purchases'));
        const deletedSeletectedUrl = @json(route('selected-purchases.remove'));
        const cat = 'purchases';
        const token = "{{ csrf_token() }}";
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //code that displays results of the table index()
            let table = $('.purchase-table');
            let title = "List of purchased items in the system";
            let columns = [0, 1, 2, 3, 4, 5, 6, 7];
            let dataColumns = [{
                    data: 'checkbox',
                    name: 'checkbox'
                },
                {
                    data: 'item',
                    name: 'item'
                },
                {
                    data: 'item_code',
                    name: 'item_code'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'cost_price_per_item',
                    name: 'cost_price_per_item'
                },
                {
                    data: 'total_cost_price',
                    name: 'total_cost_price'
                },
                {
                    data: 'date_of_purchase',
                    name: 'date_of_purchase'
                },
                {
                    data: 'supplier',
                    name: 'supplier'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ];

            makeDataTable(table, title, columns, dataColumns);

            onSelectItem();
            onClickSubmitBtn();


            function onSelectItem() {
                $('.item-name').on('change', function() {
                    let item_id = $(this).find(":selected").val();
                    if (item_id) {
                        populateStockItemDetails(item_id);
                    }
                });
            }

            function populateStockItemDetails(item_id) {

                let url = "{{ route('stock.item.find', ':id') }}";
                url = url.replace(':id', item_id);
                $.get(url, function(response) {
                    if (response.success) {
                        let data = response.data;
                        $(".item_code").val(data.item_code);
                        $(".cost_price").val(data.buying_price);
                        $(".retail_price").val(data.selling_price);
                        $(".wholesale_price").val(data.wholesale_price);
                        $(".supplier").val(data.supplier_id);
                    } else {
                        ShowResponse('.response', response.error, 'error');
                    }
                });
            }

            $('#createNewpurchase').click(function(e) {
                e.preventDefault();
                NullifyFields();
                ShowHideBtns('show');
                $('.addPurchaseBtn').html("<i class='fa fa-plus-circle pr-1'></i>Submit");
                $('#purchaseForm').trigger("reset");
                $('#modalHeading').html("Add new purchase");
                DisableFormFields(false);
                $('#addPurchaseModal').modal('show');

            });

            Numberize(".quantity");
            Numberize(".cost_price");
            Numberize(".retail_price");
            Numberize(".wholesale_price");

            function Numberize(i) {
                $(document).on("keyup", i, function() {
                    if (this.value.length > 0) {
                        let n = parseInt(this.value.replace(/\D/g, ''), 10);
                        $(this).val(n.toLocaleString());
                    }
                });
            }
            //modal used to edit purchase details [each row of the tbl]
            $('body').on('click', '#edit-purchase', function(event) {
                let purchase_id = $(this).data('id');
                event.preventDefault();
                HideContentOnEditing('hide');

                ShowHideBtns('show');
                $('.addPurchaseBtn').text("Update");
                $('#addPurchaseModal').modal('show');
                let Url = "{{ route('purchases.show', ':id') }}";
                Url = Url.replace(':id', purchase_id);
                $.ajax({

                    url: Url,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {

                        $('#modalHeading').html("Edit details of purchase item " + data.item +
                            "");
                        $('.purchaseId').val(purchase_id);
                        $('.serial_no').val(data.serial_no);
                        $('.receipt_no').val(data.receipt_no);
                        $('.item_code').val(data.item_code);
                        $('.item-name').val(data.item_id);
                        $('.quantity').val(data.quantity);
                        $('.cost_price').val(data.cost_price_per_item);
                        $('.retail_price').val(data.retail_price);
                        $('.wholesale_price').val(data.wholesale_price);
                        $('.category').val(data.catgeory_id);
                        $('.supplier').val(data.supplier_id);
                        $('.date_of_purchase').val(data.date_of_purchase);
                        $('.item-name').css('pointer-events', 'none');
                        DisableFormFields(true);
                        DisableFormFields(false);
                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                    }
                });

            });

            function UpdatePurchase(purchase_id) {

                $('.errors-section').html('');
                $('.addPurchaseBtn').html('Updating item...');

                let Url = "{{ route('purchases.update', ':id') }}";
                Url = Url.replace(':id', purchase_id);
                $.ajax({
                    data: $('#purchaseForm').serialize(),
                    url: Url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(response) {

                        let message = response.success || response.error;
                        let type = response.success ? 'success' : 'error';

                        if (response.success) {
                            $('#purchaseForm').trigger("reset");
                            $('#addPurchaseModal').modal("hide");
                            let data = response.data;
                            ResetTblInfo(data);
                            let tbl = $('.purchase-table').DataTable();
                            tbl.ajax.reload();
                        }

                        ShowResponse('.response', message, type);

                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                        $('.addPurchaseBtn').html('Save Changes');
                    }
                });


            }

            function RecordPurchase() {

                $('.errors-section').html('');
                $('.addPurchaseBtn').html('Sending data..');

                $.ajax({
                    data: $('#purchaseForm').serialize(),
                    url: "{{ route('purchases.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {

                        let message = response.success || response.error;
                        let type = response.success ? 'success' : 'error';

                        if (response.success) {
                            $('#purchaseForm').trigger("reset");
                            $('#addPurchaseModal').modal("hide");
                            let data = response.data;
                            ResetTblInfo(data);
                            let tbl = $('.purchase-table').DataTable();
                            tbl.ajax.reload();
                        }

                        ShowResponse('.response', message, type);
                    },
                    error: function(data) {
                        console.log('Error:', data.error);
                        ShowResponse('.response', data.error, 'error');
                        $('.addPurchaseBtn').html('Save Changes');
                    }
                });

            }

            //View Modal used to view each row [purchase details]
            $('body').on('click', '#view-purchase', function(event) {
                let purchase_id = $(this).data('id');
                event.preventDefault();
                HideContentOnEditing('show');
                let showUrl = '{{ route('purchases.show', ':id') }}';
                showUrl = showUrl.replace(":id", purchase_id);
                ShowHideBtns('hide');
                $.ajax({
                    url: showUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let qty = FormatNumber(data.quantity);
                        let cprice = FormatNumber(data.cost_price_per_item);
                        let sprice = data.selling_price;
                        $('#modalHeading').html("Details of purchase item " + data.item + "");
                        $('#addPurchaseModal').modal('show');
                        $('.purchaseId').val(purchase_id);
                        $('.serial_no').val(data.serial_no);
                        $('.receipt_no').val(data.receipt_no);
                        $('.item_code').val(data.item_code);
                        $('.item-name').val(data.item);
                        $('.quantity').val(data.quantity);
                        $('.cost_price').val(data.cost_price_per_item);
                        $('.retail_price').val(data.retail_price);
                        $('.wholesale_price').val(data.wholesale_price);
                        $('.supplier').val(data.supplier);
                        $('.supplier_contact').val(data.supplier_contact);
                        $('.date_of_purchase').val(data.date_of_purchase);
                        DisableFormFields(true);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            });

            function FormatNumber(number) {
                let FormattedNumber = parseInt(number).toLocaleString('us', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                return FormattedNumber;
            }

            function FormatDate(givenDate) {
                let result = moment(givenDate).format('dd-MM-yyyy');
                return result;
            }


            function onClickSubmitBtn() {
                $('.addPurchaseBtn').click(function(e) {
                    let id = $(".purchaseId").val();
                    e.preventDefault();
                    let Errors = validateForm();
                    if (Errors.length == 0) {
                        if (id) {
                            UpdatePurchase(id);

                        } else {
                            RecordPurchase();
                        }

                    } else {
                        let i;
                        let message = "";
                        for (i = 0; i < Errors.length; i++) {
                            message += Errors[i] + "<br>";
                        }
                        //ShowResponse('.errors-section', resp, 'error');
                        $('.errors-section').html(message);

                    }

                });
            }


            //this pops up confirm delete modal
            $('body').on('click', '#delete-purchase', function(e) {
                let purchase_id = $(this).data("id");
                $("#deletepurchaseModal").modal('show');
                $('.delete-ok-btn').on('click', function() {
                    ListenAndDoDeletion(purchase_id);
                });

            });


            function ListenAndDoDeletion(id) {
                let deleteUrl = '{{ route('purchases.destroy', ':id') }}';
                deleteUrl = deleteUrl.replace(':id', id);
                $('.delete-ok-btn').html('Deleting...');
                $.ajax({
                    type: "DELETE",
                    url: deleteUrl,
                    success: function(data) {
                        let resp = data.success;
                        $('.delete-ok-btn').html('Yes');
                        $('#deletepurchaseModal').modal("hide");
                        ShowResponse('.response', resp, 'success');
                        ResetTblInfo(data);
                        let tbl = $('.purchase-table').DataTable();
                        tbl.ajax.reload();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        ShowResponse('.response', data.error, 'error');
                    }
                });
            }

            function NullifyFields() {

                $('.item_code').val('');
                $('.item-name').val('');
                $('.quantity').val('');
                $('.cost_price').val('');
                $('.total_cost').val('');
                $('#supplier').val('');
                $('.record_date').val('');
            }



            function DisableFormFields(bool) {

                $('.purchaseId').attr('disabled', bool);
                $('.item_code').attr('disabled', bool);
                $('.item-name').attr('disabled', bool);
                $('.quantity').attr('disabled', bool);
                $('.cost_price').attr('disabled', bool);
                $('.total_cost').attr('disabled', bool);
                $('#supplier').attr('disabled', bool);
                $('.record_date').attr('disabled', bool);
            }

            function HideContentOnEditing(action) {
                if (action == 'hide') {
                    $('.TcostDiv').hide();
                    $('.recordedByDiv').hide();
                } else if (action == 'show') {
                    $('.TcostDiv').show();
                    $('.recordedByDiv').show();
                }

            }

            function ShowHideBtns(action) {

                if (action == 'hide') {
                    $('.addPurchaseBtn').hide();
                    $('.clearBtn').hide();
                    $('.closeBtn').hide();
                } else if (action == 'show') {
                    $('.addPurchaseBtn').show();
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

            function ResetTblInfo(data) {
                let totl_no, totl_purchases;

                if (data.totl) {
                    totl_no = FormatNumber(data.totl);
                    $('.totl-no').html(totl_no);
                }

                if (data.value) {
                    totl_purchases = FormatNumber(data.value);
                    $('.totl-purchases').html(totl_purchases);
                }

            }

            function validateForm() {

                let item = $('.item-name').val();
                let qty = $('.quantity').val();
                let cost_price = $('.cost_price').val();
                let retail_price = $('.retail_price').val();
                let supplier = $('.supplier').val();
                let dop = $('.date_of_purchase').val();

                let errors = [];

                if (item.length < 1) {
                    errors.push("Please select purchased stock item");
                }
                if (qty.length < 1) {
                    errors.push("Please enter quantity of the purchased item");
                }
                if (!cost_price) {
                    errors.push("Please enter valid cost price of the purchase");
                }
                if (!retail_price) {
                    errors.push("Please enter valid retail price of the purchase");
                }
                if (supplier.length < 1) {
                    errors.push("Please select supplier");
                }
                if (dop.length < 1) {
                    errors.push("Please select the date of purchase");
                }
                return errors;

            }


            $("#removeAllPurchases").bind("click", function() {
                RemoveAllPurchases();
            });

            function RemoveAllPurchases() {
                $.confirm({
                    boxWidth: '30%',
                    icon: 'fa fa-warning',
                    theme: 'light',
                    closeIcon: true,
                    draggable: true,
                    closeIconClass: 'fa fa-close text-danger',
                    title: 'Delete all purchases',
                    content: 'Are you sure you want to remove all purchases',
                    buttons: {
                        confirm: function() {
                            let self = this;
                            return $.ajax({
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                },
                                url: '{{ Route('purchases.truncate') }}',
                                type: 'POST',
                                // dataType: 'json',
                            }).done(function(data) {

                                $.alert({
                                    title: 'Message',
                                    content: data.success,
                                });
                                $(".totl-no").text(data.totl_no);
                                $(".totl-purchases").text(data.totl_purchases);
                                let tbl = $('.purchase-table').DataTable();
                                tbl.ajax.reload();


                            }).fail(function(data) {
                                $.alert({
                                    title: 'Response',
                                    content: "Purchases not deleted:" + data.fail,
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
