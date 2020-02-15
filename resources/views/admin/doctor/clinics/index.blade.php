@extends('layouts.admin.admin-master')

@section('title', $auth->account->type == 0 ? trans('lang.manage_clinics_poly') : trans('lang.manage_clinics'))

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ $auth->account->type == 0 ? trans('lang.manage_clinics_poly') : trans('lang.manage_clinics') }}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('clinics.create') }}" id="clinic-add"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($clinics) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                @if($auth->account->type == 0)
                                    <th class="center">{{trans('lang.address')}}</th>
                                @else
                                    <th class="center">{{trans('lang.name')}}</th>
                                @endif
                                <th class="center">{{trans('lang.fees')}}</th>
                                <th class="center">{{trans('lang.pattern')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($clinics as $clinic)
                                <tr>
                                    <td class="center">{{ $clinic[app()->getLocale() .  (($auth->account->type == 0) ? '_address' : '_name')] }}</td>
                                    <td class="center">{{ $clinic->fees }} EP</td>
                                    <td class="center">
                                        @if($clinic->pattern == 0)
                                            {{ trans('lang.intervals') }}
                                        @else
                                            {{ trans('lang.queuing') }}
                                        @endif
                                    </td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{route('clinics.edit', $clinic->id)}}"><i
                                                        class="ace-icon fa fa-edit bigger-120  edit" data-id=""></i></a>
                                            <a href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                           data-id="{{$clinic->id}} "
                                                           data-link="{{route('clinics.destroy', $clinic->id)}}"
                                                           data-type="DELETE"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_branches.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{ ($auth->account->type == 0) ? trans('lang.no_branches') : trans('lang.no_branches_poly') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@push('more-scripts')
    <script>
        $(document).ready(function () {
            var URL = "{{ url('/') }}";
            // for tour
            $.ajax({
                url: URL + '/check-first-time',
                type: 'POST',
                data: {_token: token, column_name: 'clinic_tour'}
            }).done(function (data) {
                if (data == 'true') {
                    var tour = new Tour({
                        debug: true,
                        steps: [
                            {
                                element: "#clinic-add",
                                title: "Add Clinic",
                                content: "input your clinic information and add its branches if existed",
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
