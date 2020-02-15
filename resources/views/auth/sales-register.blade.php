@extends('layouts.frontend.auth')

@section('title', trans('lang.register'))
@section('styles')
    {!! Html::style('/assets/css/auth/main.css') !!}
    {!! Html::style('/assets/css/auth/util.css') !!}
@endsection
<style>
    #field-icon {
        float: right;
        margin-left: -25px;
        margin-top: -25px;
        position: relative;
        z-index: 2;
    }
</style>
@section('content')

    <div class="wrap-login100 p-l-50 p-r-50 p-t-20 p-b-10">
        <div class="row">
            <div class="auth-logo">
                <a href="https://seena-app.com"><img src="{{ asset('assets/images/logo/logo-125.png') }}"></a>
            </div>
        </div>
        <span class="login100-form-title p-b-25 p-t-40 loon">
						Sales Register
					</span>

        <form class="login100-form login-form" action="{{route('postSalesRegister')}}" method="post">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}"/>

            <div class="wrap-input100">
                <input id="name" type="text" name="name" class="input100"
                       placeholder="Name" value="{{ old('name') }}">
                <small class="red">{{  $errors->has('name') ? $errors->first('name') : '' }}</small>
            </div>

            <div class="wrap-input100">
                <input id="email" type="email" name="email" class="input100"
                       placeholder="somebody@example.com" value="{{ old('email') }}">
                <small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>
            </div>

            <div class="wrap-input100">
                <input id="password" type="password" name="password"
                       class="input100"
                       placeholder="Password">
                <span id="field-icon" class="glyphicon glyphicon-eye-open" style="font-size: 17px"></span>
                <small class="red">{{  $errors->has('password') ? $errors->first('password') : '' }}</small>
            </div>

            <div class="wrap-input100">
                <input id="mobile" type="text" name="mobile" class="input100"
                       placeholder="Mobile" value="{{ old('mobile') }}">
                <small class="red">{{  $errors->has('mobile') ? $errors->first('mobile') : '' }}</small>
            </div>
            <div class="wrap-input100">
                <select id="type" name="type" class="input100 form-control">
                    <option value="">Choose your type</option>
                    <option value="0" {{ old('type') == '0' ? ' selected' : '' }}>Single Doctor</option>
                    <option value="1" {{ old('type') == '1' ? ' selected' : '' }}>PolyClinic</option>
                </select>
                <small class="red">{{  $errors->has('type') ? $errors->first('type') : '' }}</small>
            </div>

            <div class="wrap-input100">
                <input id="email" type="email" name="sales_email" class="input100"
                       placeholder="sales mail" value="{{ old('sales_email') }}">
                <small class="red">{{  $errors->has('sales_email') ? $errors->first('sales_email') : '' }}</small>
            </div>

            <input type="submit" id="register-btn"
                   class="btn btn-custom btn-lg btn-block background-loon"
                   value="Create your account">
        </form>

        <div class="row">
            <div class="text-center">
                <a href="{{ route('login') }}">Already have account</a>
            </div>
        </div>
    </div>
@stop
