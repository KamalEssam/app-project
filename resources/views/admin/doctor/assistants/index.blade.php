@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_assistants') )

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ trans('lang.manage_assistants') }}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('assistants.create') }}" id="assistant-add"
                       class="btn btn-sm btn-primary btn-block btn-add trigger-modal">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($assistants) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{ trans('lang.name') }}</th>
                                <th class="center">{{ trans('lang.mobile') }}</th>
                                <th class="center">{{ trans('lang.email') }}</th>
                                <th class="center">{{ trans('lang.clinic') }}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($assistants as $assistant)
                                <tr>
                                    <td class="center">{{ Super::getProperty( $assistant->name ) }}</td>
                                    <td class="center">{{ Super::getProperty( $assistant->mobile ) }}</td>
                                    <td class="center">{{ Super::getProperty( $assistant->email ) }}</td>
                                    <td class="center">{{ Super::getProperty(Super::min_address($assistant->clinic[App::getLocale() . '_address'])) }}</td>

                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('assistants.edit',$assistant->id) }}">
                                                <i class="ace-icon fa fa-edit bigger-120 edit" data-id=""></i></a>
                                            <a href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                           data-id="{{ $assistant->id }} "
                                                           data-link="{{ route('assistants.destroy', $assistant->id) }}"
                                                           data-type="DELETE"></i></a>
                                            <a href="#"
                                               data-link="{{ route('assistants.reset-password',$assistant->id) }}"
                                               data-id="{{ $assistant->id }} "
                                               title="reset password" class="reset-password">
                                                <i class="ace-icon fa fa-key bigger-120 reset-password" data-id=""></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_assistants.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_assistants')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('more-scripts')
    <script>
        $(document).on('click', '.reset-password', function (e) {

            var id = $(this).data('id');
            var link = $(this).data('link');

            swal({
                    title: "Please confirm",
                    text: "Are you going to reset the assistant's password ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $.ajax({
                        url: link,
                        type: 'POST',
                        data: {_token: token, id: id}
                    }).done(function (data) {
                        console.log(data.status);
                        if (data.status === true) {
                            swal({
                                title: "Done",
                                text: "We sent password to assistant's phone number",
                                type: "success",
                            });
                        } else {
                            swal({
                                title: "Failure",
                                text: "Failed reset password",
                                type: "error",
                            });
                        }
                    });
                });
        });
        $(document).ready(function () {
            var URL = "{{ url('/') }}";
            // for tour
            $.ajax({
                url: URL + '/check-first-time',
                type: 'POST',
                data: {_token: token, column_name: 'assistant_tour'}
            }).done(function (data) {
                if (data == 'true') {
                    var tour = new Tour({
                        debug: true,
                        steps: [
                            {
                                element: "#assistant-add",
                                title: "Add Assistant",
                                content: "Add your current/Additional assistant here.",
                                placement: "bottom",
                                backdrop: true,
                                template: "<div class='popover tour'>" +
                                "<div class='arrow'></div>" +
                                "<h3 class='popover-title'></h3>" +
                                "<div class='popover-content'></div>" +
                                "<div class='popover-navigation'>" +
                                "<button class='btn btn-default' data-role='end'>Got it!</button>" +
                                "</div>" +
                                "</div>",
                            }
                        ]
                    });
                    tour.init();
                    tour.restart();
                }
            });
        });

    </script>
@endpush