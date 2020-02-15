@extends('layouts.home.master')

<!-- comment -->
@section('title', 'Home')

@section('content')
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-4 form-spaces">
                    <div id="login">
                        <h1 class="white">Log in with your email account</h1>
                        <hr>
                        <form role="form" action="{{route('post.login')}}" method="post" id="login-form"
                              autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="">
                                <small class="red">{{  $errors->has('email') ? $errors->first('email') : '' }}</small>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="">
                                <small class="red">{{  $errors->has('password') ? $errors->first('password') : '' }}</small>
                            </div>
                            <input type="submit" id="btn-login"
                                   class="btn btn-custom btn-lg btn-block background-loon"
                                   value="{{trans('lang.login')}}">
                        </form>
                        <a href="{{url('/')}}/password/reset" class="forget">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop