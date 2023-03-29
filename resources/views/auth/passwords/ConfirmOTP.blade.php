@extends('layouts.app')

@section('content')
    <div class="container">

        <div class=" pt-5">
            <h4 class="text-center text-white custom-family">
                {{ config('app.name') }}
            </h4>
            <h6 class="nunito-font text-center text-white text-muted">
                <small>&copy; {{ config('app.name') }} {{ date('Y') }}</small>
            </h6>
        </div>

        <div class="row justify-content-center pl-5 nunito-font">

            <div class="col-md-6">
                <div class="card margin-top-form">
                    <div class="card-header  overview-item--c4">
                        <h6> {{ __('Confirm Login with OTP') }}</h6>
                    </div>

                    <div class="card-body">

                        <div class="text-center text-danger">
                            {{ __('* Please enter OTP.') }}
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="OTP" class="col-md-4 col-form-label text-md-right">
                                    {{ __('OTP') }}
                                </label>

                                <div class="col-md-6">
                                    <input id="OTP" type="password"
                                        class="OTP form-control @error('OTP')
                                  is-invalid @enderror"
                                        name="OTP" required autocomplete="off">
                                    <small class="text-decoration-none text-info showOTP">
                                    </small>
                                    @error('OTP')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-85 overview-item--c4 text-white">
                                        <strong>{{ __('Verify OTP') }}</strong>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendors/js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('js/login/login.js') }}"></script>
@endsection
