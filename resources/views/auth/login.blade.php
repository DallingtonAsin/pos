@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3 login mt-5">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('authenticate') }}">
                            @csrf
                            <div class="header nunito-font">
                                <div class="row justify-content-center ">
                                    @if (isset($company) && isset($company->logo))
                                        <img src="{{ asset('uploads/images/company/logo/' . $company->logo . '') }}"
                                            class="co-icon-center" alt="">
                                    @else
                                        <img src="{{ asset('uploads/images/company/logo/default/brand.jpg') }}"
                                            class="co-icon-center">
                                    @endif
                                </div>
                                <div class="row justify-content-center">
                                    <label
                                        class="col-form-label text-dark font-weight-bold text-md-center">{{ $company->name }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <h6 class="text-dark nunito-font ">Sign in to start your session</h6>
                            </div>
                            <div class="form-group">
                                <span class="login_span bolded nunito-font">Username or email</span>
                                <input id="pos_login" type="text"
                                    class="form-control nunito-font @error('pos_login') is-invalid @enderror"
                                    name="pos_login" value="{{ old('pos_login') }}"
                                    placeholder="Enter your email or username" required autocomplete="email" autofocus
                                    spellcheck="false">
                            </div>

                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="login_span nunito-font bolded">Password</span>
                                    </div>
                                </div>

                                <input type="password" class="password form-control nunito-font" id="password"
                                    @error('pos_password') is-invalid @enderror nunito-font" name="pos_password"
                                    placeholder="Enter your password" value="{{ old('pos_password') }}" autocomplete="off"
                                    required>
                                <small class="text-decoration-none text-info showPwd">
                                </small>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label nunito-font" for="remember">
                                            <small>{{ __('Remember me') }}</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if (Route::has('password.request'))
                                        <a class="nunito-font" href="{{ route('password.request') }}">
                                            <small>{{ __('Forgot Password?') }}</small>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-block text-white bg-success bolded">
                                    <strong>{{ __('Sign in') }}</strong>
                                </button>
                            </div>

                            <div class="form-group px-0 py-0">
                                @if ($errors->any())
                                    <span class="login-error nunito-font">
                                        <span>{{ $errors->first() }}</span>
                                    </span>
                                @endif

                                @if (session()->get('loginErr'))
                                    <span class="login-error nunito-font">
                                        {{ session()->get('loginErr') }}
                                    </span>
                                @endif

                                @if (session()->get('sessionExpiredMessage'))
                                    <span class="login-error nunito-font">
                                        {{ session()->get('sessionExpiredMessage') }}
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="{{ asset('vendors/js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('js/login/login.js') }}"></script>
@endsection