@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_sales') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-7">
                    @php
                        if (Request::has('status')) {
                            switch (Request::get('status')) {
                                case 1:
                                $status = 'account with no image';
                                break;
                                 case 2:
                                  $status = 'account with no bio';
                                break;
                                 case 3:
                                  $status = 'in active account';
                                break;
                                 case 4:
                                 $status = 'account with no services';
                                break;
                                default:
                                 $status = 'all';
                                break;
                            }
                        } else {
                            $status =  'all';
                        }
                    @endphp
                    <h1>{{trans('lang.sales_logs') . ' ( '. $status . ' )'}}</h1>
                </div>

                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-3"><a class="btn btn-sm btn-warning btn-block btn-import"
                                                 href="?status=1">no-image</a></div>
                        <div class="col-md-3"><a class="btn btn-sm btn-warning btn-block btn-export" href="?status=2">no-bio</a>
                        </div>
                        <div class="col-md-3"><a class="btn btn-sm btn-warning btn-block btn-add"
                                                 href="?status=3">in-active</a></div>
                        <div class="col-md-3"><a class="btn btn-sm btn-warning btn-block btn-delete" href="?status=4">no-services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($accounts) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center" style="width: 50px !important;">{{trans('lang.image')}}</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.email')}}</th>
                                <th class="center">{{trans('lang.account_name')}}</th>
                                <th class="center">{{trans('lang.mobile')}}</th>
                                <th class="center">{{trans('lang.type')}}</th>
                                <th class="center">{{trans('lang.is_published')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($accounts as $doctor)
                                <tr>
                                    <td class="center">
                                        <div class="premium-container">
                                            <img class="premium-image"
                                                 src="{{ ($doctor->image == 'default.png') ?  asset('assets/images/' . $doctor->image)  : asset('assets/images/profiles/' . $doctor->unique_id . '/' . $doctor->image) }}"
                                                 alt="">
                                            @if ($doctor->is_premium == 1)
                                                <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                                     class="premium-icon">
                                            @endif
                                        </div>
                                    </td>

                                    <td class="center">{{ $doctor->name }}</td>
                                    <td class="center">{{ $doctor->email }}</td>
                                    <td class="center">{{ $doctor->account_name }}</td>
                                    <td class="center">{{ $doctor->mobile  }}</td>
                                    <td class="center">{{ $doctor->type == 1 ? trans('lang.poly') : trans('lang.single')  }}</td>
                                    <td class="center">{{ $doctor->is_published ? trans('lang.yes') : trans('lang.no') }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            @if($doctor->is_published == 0 || $doctor->is_published == 2)
                                                <a href="{{  url('accounts/'.$doctor->account_id.'/publish/true')  }}"
                                                   title="{{ trans('lang.publish_account') }}" style="margin:1px">
                                                    <i class="ace-icon fa fa-lg fa-eye bigger-120 edit"
                                                       data-id="{{ $doctor->id }}"></i>
                                                </a>
                                            @else
                                                <a href="{{ url('accounts/'.$doctor->account_id.'/publish/false')   }}"
                                                   title="{{ trans('lang.un_publish_account') }}"
                                                   style="margin:1px">
                                                    <i class="ace-icon fa fa-lg fa-eye-slash bigger-120 edit"
                                                       data-id=""></i>
                                                </a>
                                            @endif

                                            <a href="#" data-id="{{ $doctor->id }}" id="account_status"
                                               title=" account stages">
                                                <i class="ace-icon fa fa-lg fa-comment bigger-120 delete"></i>
                                            </a>

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
                                                                src="{{ asset('assets/images/no_data/no_sales.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_logs')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>


@stop


@push('more-scripts')
    <script>
        $(document).on('click', '#account_status', function (e) {

            // ajax to call the account data
            // image - bio - in-active - services
            let user_id = $(this).data('id');
            $.ajax({
                url: URL + '/sale/account-steps',
                type: 'GET',
                data: {
                    user_id: user_id
                }
            }).done(function (data) {

                let results = '';
                let notification_counter = 0;

                if (data[0] == true) {
                    results += '<tr>' +
                        '<th class="center">Image</th>' +
                        '<td>' +
                        '<i class="fa fa-check green"></i>' +
                        '</td>' +
                        '</tr>';
                } else {
                    results += '<tr>' +
                        '<th class="center">Image</th>' +
                        '<td>' +
                        '<i class="fa fa-times red"></i>' +
                        '</td>' +
                        '</tr>';
                    notification_counter++;
                }


                if (data[1] == true) {
                    results += '<tr>' +
                        '<th class="center">BIO</th>' +
                        '<td>' +
                        '<i class="fa fa-check green"></i>' +
                        '</td>' +
                        '</tr>';
                } else {
                    results += '<tr>' +
                        '<th class="center">BIO</th>' +
                        '<td>' +
                        '<i class="fa fa-times red"></i>' +
                        '</td>' +
                        '</tr>';
                    notification_counter++;
                }


                if (data[2] == true) {
                    results += '<tr>' +
                        '<th class="center">Active</th>' +
                        '<td>' +
                        '<i class="fa fa-check green"></i>' +
                        '</td>' +
                        '</tr>';
                } else {
                    results += '<tr>' +
                        '<th class="center">Active</th>' +
                        '<td>' +
                        '<i class="fa fa-times red"></i>' +
                        '</td>' +
                        '</tr>';
                }

                if (data[3] == true) {
                    results += '<tr>' +
                        '<th class="center">Services</th>' +
                        '<td>' +
                        '<i class="fa fa-check green"></i>' +
                        '</td>' +
                        '</tr>';
                } else {
                    results += '<tr>' +
                        '<th class="center">Services</th>' +
                        '<td>' +
                        '<i class="fa fa-times red"></i>' +
                        '</td>' +
                        '</tr>';
                    notification_counter++;
                }

                let notification_btn = '';
                if (notification_counter > 0) {
                    notification_btn = '<a href="#" data-id="' + user_id + '" data-status="' + data + '"  id="send_notification" class="btn btn-sm btn-primary"> send notification </a> ';
                }
                let table = '<table id="dynamic-table" class="table table-striped table-bordered table-hover"' + results + '</table>' + notification_btn;

                swal({
                    title: 'Stages',
                    text: '<b>' + table + '</b>',
                    html: true
                });
            });
        });

        $(document).on('click', '#send_notification', function (e) {
            console.log('notifications');
            $.ajax({
                url: URL + '/account/send-notifications',
                type: 'POST',
                data: {
                    user_id: $(this).data('id'),
                    status: $(this).data('status')
                }
            }).done(function (data) {
                if (data.status == true) {
                    location.reload();
                }
            });
        });
    </script>
@endpush
