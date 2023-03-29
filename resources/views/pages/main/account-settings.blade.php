@extends('layouts.master')

@section('content')
    <div class="card card-dashboard-table-six">
        <div class="card-title">
            <strong class="col-lg-10 text-success">
                <i class="fa fa-gear"></i>
                <span class="text-dark">
                    Profile / Edit your profile
                </span>
            </strong>
        </div>

        <div class="card-body pt-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="profileForm" method="POST" action="{{ route('profile.update', Auth::user()->id) }}"
                        id="profileForm" enctype='multipart/form-data'>
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <span class="text-muted">Username</span>
                            <input type="hidden" class="form-control user_id" name="id" value="{{ Auth::user()->id }}"
                                autocomplete="off">
                            <input type="text" class="form-control user_name" name="username"
                                value="{{ Auth::user()->username }}" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <span class="text-muted">Email</span>
                            <input type="text" class="form-control email" name="email"
                                value="{{ Auth::user()->email }}" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <span class="text-muted">Contact</span>
                            <input type="text" class="form-control contact" name="contact"
                                value="{{ Auth::user()->tel_no }}" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <span class="text-muted">Address</span>
                            <input type="text" class="form-control address" name="address"
                                value="{{ Auth::user()->address }}" autocomplete="off">
                        </div required>

                        <div class="form-group">
                            <span class="text-muted">Image</span>
                            <input type="file" class="form-control-file" name="image">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Need to change your password? click here
                            </button>
                        </div>

                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon text-muted">
                                        Old password
                                    </span>
                                    <input type="password" id="oldpassword" name="old_password"
                                        class="form-control oldpassword" value="{{ old('old_password') }}"
                                        placeholder="Enter your old password">
                                    <span class="input-group-addon" id="showpassword1" required>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon text-muted">
                                        <i class="glyphicon glyphicon-eye"></i>New password
                                    </span>
                                    <input type="password" id="newpassword" name="new_password"
                                        class="form-control newpassword" value="{{ old('new_password') }}"
                                        placeholder="Enter your new password">
                                    <span class="input-group-addon" id="showpassword2" required>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon text-muted">
                                        <i class="glyphicon glyphicon-eye"></i>Confirm password
                                    </span>
                                    <input type="password" id="confirmpassword" name="password_confirm"
                                        class="form-control confirmpassword" value="{{ old('password_confirm') }}"
                                        placeholder="Confirm your password">
                                    <span class="input-group-addon" id="showpassword3" required>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="row form-group">
                            <div class="col-lg-3">
                                <button type="submit" class="btn btn-success addProfileBtn" name="addProfileBtn">Update
                                    Profile</button>
                            </div>

                            <div class="col-lg-9">
                                <span class="pl-0 errors_section text-danger"></span>
                            </div>

                            <div class="col-lg-9">
                                <span class="pl-0 response"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}


    <script src="{{ asset('vendors/notify/notify.js') }}"></script>

    @if (session()->get('success'))
        <script>
            $(document).ready(function() {
                let div = ".response";
                let type = "success";
                let LoginMessageError = "{{ session()->get('success') }}";
                ShowLoginErrorMessage(div, type, LoginMessageError);
            });
        </script>
    @endif


    @if (session()->get('error'))
        <script>
            $(document).ready(function() {
                let div = ".response";
                let type = "error";
                let LoginMessageError = "{{ session()->get('error') }}";
                ShowLoginErrorMessage(div, type, LoginMessageError);
            });
        </script>
    @endif


    <script>
        let oldpassword = document.getElementById("oldpassword");
        let newpassword = document.getElementById("newpassword");
        let confirmpassword = document.getElementById("confirmpassword");

        $('#showpassword1').click(function() {
            if (oldpassword.type === "password") {
                oldpassword.type = "text";
            } else {
                oldpassword.type = "password";
            }
        });

        $('#showpassword2').click(function() {
            if (newpassword.type === "password") {
                newpassword.type = "text";
            } else {
                newpassword.type = "password";
            }
        });

        $('#showpassword3').click(function() {
            if (confirmpassword.type === "password") {
                confirmpassword.type = "text";
            } else {
                confirmpassword.type = "password";
            }
        });

        // $('.addProfileBtn').click(function (e) {
        //     let id = $(".user_id").val();
        //     e.preventDefault();
        //     let Errors = validateForm();
        //     if(Errors.length == 0){
        //         if(id){
        //             UpdateProfile(id);
        //         }
        //     }else
        //     {
        //         let i;
        //         let message ="";
        //         for(i=0; i<Errors.length; i++){
        //             message += Errors[i] + "<br>";
        //         }
        //         $('.errors_section').html(message);
        //     }
        // });

        function UpdateProfile(user_id) {
            $('.errors_section').html('');
            $('.addProfileBtn').html('Updating profile...');

            let Url = "{{ route('profile.update', ':id') }}";
            Url = Url.replace(':id', user_id);
            $.ajax({
                data: $('#profileForm').serialize(),
                url: Url,
                type: "PUT",
                dataType: 'json',
                success: function(data) {
                    getUpdatedUserDetails(user_id);
                    if (data.success) {
                        ShowResponse('.response', data.success, 'success');
                        $('.oldpassword').val('');
                        $('.newpassword').val('');
                        $('.confirmpassword').val('');
                    }
                    if (data.error) {
                        ShowResponse('.response', data.error, 'error');
                    }
                    $('.addProfileBtn').html("Update");

                },
                error: function(data) {
                    console.log('Error:', data.error);
                    ShowResponse('.response', data.error, 'success');
                    $('.addProfileBtn').html('Save Changes');
                }
            });

        }


        function validateForm() {
            let username = $('.user_name').val();
            let contact = $('.contact').val();
            let address = $('.address').val();
            let oldpassword = $('.oldpassword').val();
            let newpassword = $('.newpassword').val();
            let confirmpassword = $('.confirmpassword').val();

            let errors = [];
            if (username.length < 1) {
                let usernameErr = "Please enter your name";
                errors.push(usernameErr);
            }
            if (contact.length < 1) {
                let contactErr = "Please enter your contact";
                errors.push(contactErr);
            }
            if (address.length < 1) {
                let addresErr = "Please enter your address";
                errors.push(addresErr);
            }

            if (oldpassword) {
                if (stringIsEmpty(newpassword) == true) {
                    errors.push("Please enter your new password");
                }

                if (stringIsEmpty(confirmpassword) == true) {
                    errors.push("Please confirm your new password");
                }

                if (newpassword && confirmpassword) {
                    if (newpassword !== confirmpassword) {
                        errors.push("Please enter matching passwords");
                    }
                }
            }
            return errors;
        }

        function stringIsEmpty(value) {
            return value ? value.trim().length == 0 : true;
        }

        function ShowResponse(area, message, errorType) {
            $(area).notify(message, {
                className: errorType,
                autoHide: true,
                clickToHide: true,
                autoHideDelay: 45000,
            });
        }

        function getUpdatedUserDetails(user_id) {
            $.get("{{ route('profile.index') }}" + '/' + user_id + '', function(data) {
                $('.user_name').val(data.username);
                $('.email').val(data.email);
                $('.contact').val(data.tel_no);
                $('.address').val(data.address);
            });
        }
    </script>
@endsection