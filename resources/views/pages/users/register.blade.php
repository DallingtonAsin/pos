@extends('layouts.master')

@section('content')
    {{-- <div class="card-table nunito-font"> --}}
    <div class="card card-dashboard-table-six">

        <div class="card-title">
            <div class="row">
                <div class="col-md-5">
                    <strong class="pl-1 pt-3 pb-1">
                        <span class="pl-0">{{ __('Register User') }}</span>
                    </strong>
                </div>

            </div>
        </div>


        <div class="card-body nunito-font">
            @if (session()->get('success'))
                <div class="row">
                    <div class="col-lg-8">
                        <div class='alert alert-success alert-dismissible text-center' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Yello!</strong>
                            {{ session()->get('success') }}<i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            @endif

            @if (session()->get('fail'))
                <div class="row">
                    <div class="col-lg-8">
                        <div class='alert alert-danger alert-dismissible text-center' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Sorry!</strong> {{ session()->get('fail') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <form mname="user" id="userForm" method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="fname" class="col-form-label text-muted text-md-right"><i
                                    class="text-danger">*</i>
                                {{ __('First Name') }}</label>
                            <input id="fname" type="text" class="form-control first_name" name="firstName"
                                value="{{ old('firstName') }}" placeholder="Enter cashier's first name" autocomplete="off">
                            @error('firstName')
                                <span class="text-danger fnameErr">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="col-form-label   text-muted text-md-right"><i
                                    class="text-danger">*</i>
                                {{ __('Last Name') }}</label>

                            <input id="lastName" type="text" class="form-control last_name" name="lastName"
                                value="{{ old('lastName') }}" placeholder="Enter cashier's last name" autocomplete="off">
                            @error('lastName')
                                <span class="text-danger lnameErr">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-form-label  text-muted text-md-right"><i
                                    class="text-danger">*</i>
                                {{ __('Address') }}</label>

                            <input id="address" type="text" class="form-control address" name="address"
                                value="{{ old('address') }}" placeholder="Enter address" autocomplete="off">
                            @error('address')
                                <span class="text-danger addressErr">{{ $message }}</span>
                            @enderror
                        </div>



                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label for="NIN" class="col-form-label text-muted text-md-right"><i
                                        class="text-danger">*</i>
                                    {{ __('National ID Number') }}</label>

                                <input id="NIN" type="text" class="form-control national_id" name="NationalIDNo"
                                    value="{{ old('NationalIDNo') }}" placeholder="Enter NationalID Number"
                                    autocomplete="off">
                                @error('NationalIDNo')
                                    <span class="text-danger ninErr">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="col-lg-6">
                                <label for="email" class="col-form-label text-muted text-md-right">
                                    {{ __('Email') }}</label>
                                <input id="email" type="email" class="form-control email" placeholder="Enter email"
                                    name="email" value="{{ old('email') }}" autocomplete="off">
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label for="primaryTelNo" class="col-form-label text-muted text-md-right"><i
                                        class="text-danger">*</i>
                                    {{ __('Primary Telephone No.') }}</label>

                                <input id="telno" type="text" class="form-control tel_no" name="tel_no"
                                    value="{{ old('tel_no') }}" placeholder="Enter primary telephone number"
                                    autocomplete="off">
                                @error('tel_no')
                                    <span class="text-danger telnoErr ">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="company" class="col-form-label text-muted text-md-right">
                                    {{ __('Alternative Telephone No.') }}</label>

                                <input id="company" type="text" class="form-control alt_telno" name="alt_telno"
                                    value="{{ old('alt_telno') }}" placeholder="Enter alternative telephone number"
                                    autocomplete="off">
                            </div>
                        </div>



                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span><span class="text-danger">*</span> Role</span>
                                    <select class="form-control role_section " name="role">
                                        <option value="">select role</option>
                                    </select>
                                    @error('role')
                                    <span class="text-danger telnoErr ">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span><span class="text-danger">*</span> Gender</span>
                                <select class="form-control gender " name="gender">
                                    <option value="">select gender</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>Female
                                    </option>
                                    <option value="Male" {{ old('gender') == 'Female' ? 'checked' : '' }}>Male</option>
                                </select>
                                @error('gender')
                                <span class="text-danger telnoErr ">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success text-white AdduserBtn">
                                    {{ __('Submit') }}
                                </button>
                                <button type="reset" class="btn btn-danger text-white">
                                    {{ __('Reset') }}
                                </button>
                            </div>
                            <span class="errors-section text-danger"></span>
                            <span class="response"></span>
                        </div>

                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var field = $("#fname");

            ClearErrorMessage();

            PopulateRoles();

            function PopulateRoles() {
                var fetchRolesByAjaxUrl = "{{ route('roles.ajax.fetch') }}"
                $.ajax({
                    type: "GET",
                    url: fetchRolesByAjaxUrl,
                    success: function(resp) {
                        var obj = JSON.parse(resp);
                        for (var i = 0; i < obj.length; i++) {
                            let role_id = obj[i]['id'];
                            let role_name = obj[i]['name'];
                            $('.role_section').append('<option value=' + role_id + '>' + role_name +
                                '</option>');
                        }
                    }
                });
            }


            // $('.AdduserBtn').click(function(e) {

            //     e.preventDefault();

            //     var Errors = validateForm();
            //     if (Errors.length == 0) {
            //         $('.errors-section').html('');
            //         $(this).html('Sending..');

            //         $.ajax({
            //             data: $('#userForm').serialize(),
            //             url: "{{ route('users.store') }}",
            //             type: "POST",
            //             dataType: 'json',
            //             success: function(data) {

            //                 $('#userForm').trigger("reset");
            //                 var resp = data.success;
            //                 ShowResponse('.response', resp, 'success');
            //                 $('.AdduserBtn').html('Submit');
            //             },
            //             error: function(data) {
            //                 console.log('Error:', data.error);
            //                 ShowResponse('.response', data.error, 'error');
            //                 $('.AdduserBtn').html('Save Changes');
            //             }
            //         });
            //     } else {
            //         var i;
            //         var message = "";
            //         for (i = 0; i < Errors.length; i++) {
            //             message += Errors[i] + "<br>";
            //         }
            //         $('.errors-section').html(message);

            //     }

            // });


            function ShowResponse(area, message, errorType) {
                $(area).notify(message, {
                    className: errorType,
                    autoHide: true,
                    clickToHide: true,
                    autoHideDelay: 45000,
                });
            }

            function validateForm() {
                var first_name = $('.first_name').val();
                var last_name = $('.last_name').val();
                var address = $('.address').val();
                var national_id = $('.national_id').val();
                var tel_no = $('.tel_no').val();
                var role = $('.role_section').val();
                var gender = $('.gender').val();

                var errors = [];
                if (first_name.length < 1) {
                    var fnameErr = "Please enter the first name of the cashier";
                    errors.push(fnameErr);
                }
                if (last_name.length < 1) {
                    var lnameErr = "Please enter the last name of the cashier";
                    errors.push(lnameErr);
                }
                if (address.length < 1) {
                    var addressErr = "Please enter the address of the cashier";
                    errors.push(addressErr);
                }

                if (tel_no.length < 1) {
                    var contactErr = "Please enter the primary telephone number of the cashier";
                    errors.push(contactErr);
                }
                if (role.length < 1) {
                    var roleErr = "Please enter user's role";
                    errors.push(roleErr);
                }

                if (gender.length < 1) {
                    var genderErr = "Please enter user's gender";
                    errors.push(genderErr);
                }
                return errors;
            }






            function ClearErrorMessage() {
                $("#fname").on("input", function() {
                    var input = $(".fnameErr").html();
                    if (input) {
                        input = "yesss";
                    }
                });
            }
        });
    </script>

    {{-- </div> --}}
@endsection
