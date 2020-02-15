@extends('layouts.home.master')

<!-- comment -->
@section('title', 'Home')

@section('styles')
    <style>
        .renew-container {
            margin-top: 290px !important;
        }
    </style>
@stop
@section('content')

    <div class="container renew-container">
        <div class="row">
            <div class="col-md-6 col-xs-offset-3">
                <div class="suspended-card">
                    <div class="suspended-container">
                        <strong class="loon">Suspended</strong>
                        <hr>
                        <p class="center">Your account is suspended, please contact us on 01116579777 or visit our website <a href="https://rklinic.com/">RKlinic</a></p><br>
                    </div>
                {{--    <div class="row">
                        <div class="col-md-4 col-xs-offset-4 mb-10">
                            <a href="{{ route('plans') }}" class="btn-loon btn-xs suspended-btn"> Renew </a>
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>

@stop