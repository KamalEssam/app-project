@extends('layouts.admin.admin-master')

@section('title', trans('lang.manage_app'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.manage_app')}}</h1>
                        <hr>
                            {!! Form::open(['route' => 'apps.store', 'files' => true]) !!}
                            @include('admin.common.apps.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                            {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('assets/js/admin/events.js') !!}
    {!! Html::script('assets/js/admin/select2.min.js') !!}
    <script>
        $(document).on('keyup', '#tags', function (e) {
            $.ajax({
                url: URL + '/admin/apps/search-in-unique-id',
                type: 'GET',
                data: {_token: token}
            }).done(function (data) {
                results = data
                list = results.split("#");

                //autocomplete emails
                var availableTags = list;
                $('#tags').autocomplete({
                    source: availableTags
                });
            });
        });
    </script>
@stop