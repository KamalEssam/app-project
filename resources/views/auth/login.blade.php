@extends('layouts.frontend.auth')

@section('title', trans('lang.login'))
@section('styles')
    {!! Html::style('/assets/css/auth/main.css') !!}
    {!! Html::style('/assets/css/auth/util.css') !!}
@endsection
@section('content')
    <div class="wrap-login100 p-l-50 p-r-50 p-t-40 p-b-50">

        @if(Session::has("error"))
            <div class="alert alert-danger">{{ Session::get("error")  }}</div>
        @endif

        @if(Session::has("success"))
            <div class="alert alert-success">{{ Session::get("success")  }}</div>
        @endif

        <div class="row">
            <div class="auth-logo">
                <a href="https://seena-app.com"><img src="{{ asset('assets/images/logo/logo-125.png') }}"></a>
            </div>
        </div>
        <span class="login100-form-title p-b-59 p-t-40 loon">
						Login
					</span>
        <form role="form" action="{{route('post.login')}}" method="post" id="login100-form login-form"
              autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}"/>
            <div class="wrap-input100">
                <input type="text" name="login" id="email" class="input100"
                       placeholder="mobile number or email address">
                <small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>
            </div>
            <div class="wrap-input100">
                <input type="password" name="password" id="password"
                       class="input100"
                       placeholder="Password">
                <small class="red">{{  $errors->has('password') ? $errors->first('password') : '' }}</small>
            </div>
            <input type="submit" id="login-btn"
                   class="btn btn-custom btn-lg btn-block background-loon login100-form-btn"
                   value="Log in">

        </form>
        <div class="mt-15">
            <a href="/password/reset" class="forget txt1 pull-left">{{ trans('lang.forget_your_password') }}</a>
            <a href="/register" class="forget txt1 pull-right">{{ trans('lang.sign_up') }}</a>
        </div>

    </div>
@stop
{{--
        <div>
            <div class="col-md-4 form-spaces login">
                <div id="login">
                    <h1 class="loon">RKlinic login</h1>

                    <form role="form" action="{{route('post.login')}}" method="post" id="login-form"
                          autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}"/>
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="somebody@example.com">
                            <small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="password"
                                   class="form-control"
                                   placeholder="Password">
                            <small class="red">{{  $errors->has('password') ? $errors->first('password') : '' }}</small>
                        </div>
                        <input type="submit" id="login-btn"
                               class="btn btn-custom btn-lg btn-block background-loon"
                               value="Log in">

                    </form>
                    <a href="#" class="forget">Forgot
                        your
                        password?</a>
                    <div class="row">
                        <div class="auth-logo">
                            <a href="/"><img src="{{ asset('assets/images/logo/logo-125.png') }}"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    --}}
