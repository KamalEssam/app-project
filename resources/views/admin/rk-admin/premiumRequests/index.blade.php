@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_requests') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h1>{{trans('lang.manage_requests')}}</h1>
                </div>

{{--                <div class="col-md-2">--}}
{{--                    {{ Form::select('status',['new' => 'new','declined' => 'declined','approved' => 'approved'] , (is_null($_GET['status']) ? null : (in_array($_GET['status'],['new','approved','declined'])) ? $_GET['status'] : null), ['class'=>'form-control ' . ($errors->has('status') ? 'redborder' : '') , 'id'=>'selectStatus']) }}--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($premiumRequests) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.plan')}}</th>
                                <th class="center">{{trans('lang.status')}}</th>
                                <th class="center">{{ trans('lang.price') }}</th>
                                <th class="center">{{ trans('lang.date') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($premiumRequests as $premiumRequest )
                                <tr>
                                    <td class="center">{{ $premiumRequest->user ? $premiumRequest->user->name : 'deleted'  }}</td>
                                    <td class="center">{{ $premiumRequest->plan[app()->getLocale() . '_name'] }}</td>
                                    <td class="center">
                                        @switch( $premiumRequest->approval)
                                            @case(-1)
                                            {{ trans('lang.new') }}
                                            @break
                                            @case(0)
                                            {{ trans('lang.declined') }}
                                            @break
                                            @case(1)
                                            {{ trans('lang.accepted') }}
                                            @break
                                            @default
                                            {{ trans('lang.new') }}
                                        @endswitch
                                    </td>
                                    <td class="center">{{  $premiumRequest->due_amount }}</td>
                                    <td class="center">{{ \Carbon\Carbon::parse($premiumRequest->created_at)->format('jS \o\f F, Y g:i:s a') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else

                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_plans.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_requests')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>
@stop

{{--@push('more-scripts')--}}
{{--    <script>--}}
{{--        $(document).on('click', '.changeStatus', function (e) {--}}
{{--            let status = $(this).data('status');--}}
{{--            let link = $(this).data('link');--}}
{{--            let message = (status == 0) ? "DECLINE the request" : "ACCEPT the request";--}}
{{--            let request_id = $(this).data('id');--}}
{{--            swal({--}}
{{--                    title: "Are you sure?",--}}
{{--                    text: 'are you sure you want to ' + message,--}}
{{--                    type: "warning",--}}
{{--                    showCancelButton: true,--}}
{{--                    confirmButtonClass: "btn-danger",--}}
{{--                    confirmButtonText: "Yes, I'am sure!",--}}
{{--                    closeOnConfirm: false--}}
{{--                },--}}
{{--                function () {--}}
{{--                    $.ajax({--}}
{{--                        url: link,--}}
{{--                        type: 'POST',--}}
{{--                        data: {--}}
{{--                            _token: token,--}}
{{--                            status: status,--}}
{{--                            request_id: request_id--}}
{{--                        }--}}
{{--                    }).done(function (data) {--}}
{{--                        if (data.status === false) {--}}
{{--                            // check if working status valid or not--}}
{{--                            swal({--}}
{{--                                title: "Failure",--}}
{{--                                text: data.msg,--}}
{{--                                type: "warning",--}}
{{--                            });--}}
{{--                        } else if (data.status === true) {--}}
{{--                            swal({--}}
{{--                                    title: "Done",--}}
{{--                                    text: data.msg,--}}
{{--                                    type: "success",--}}
{{--                                },--}}
{{--                                function () {--}}
{{--                                    window.location.reload();--}}
{{--                                });--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}

{{--        });--}}

{{--        $(document).on('change', '#selectStatus', function (e) {--}}
{{--            let status = $(this).val();--}}
{{--            window.location = URL + 'account/premium-requests?status=' + status;--}}
{{--        });--}}

{{--    </script>--}}
{{--@endpush--}}
