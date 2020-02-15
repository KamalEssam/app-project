@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_accounts') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_accounts')}}</h1>
                </div>
                @if($auth->role_id == $role_rk_super_admin)
                    <div class="col-md-1">
                        <a href="{{ route('accounts.create') }}"
                           class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-20 pull-right">
                        {{ Form::select('type',[trans('lang.all'), trans('lang.not_active'),trans('lang.not_published')],$_GET['type'] ?? null,['id' => 'type','class' => 'form-control']) }}
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
                                <th class="center">{{trans('lang.is_active')}}</th>
                                @if($auth->role_id == $role_rk_super_admin || $auth->role_id == $role_rk_admin)
                                    <th class="center">{{trans('lang.controls')}}</th>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td class="center">
                                        <div class="premium-container">
                                            <img class="premium-image"
                                                 src="{{ ($account->image == 'default.png') ?  asset('assets/images/' . $account->image)  : asset('assets/images/profiles/' . $account->unique_id . '/' . $account->image) }}"
                                                 alt="">
                                            @if ($account->is_premium == 1)
                                                <img src="{{ asset('assets/images/premium.png') }}" alt=""
                                                     class="premium-icon">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="{{ route('accounts.show' , $account->id ) }}">  {{ Super::getProperty($account->name ?? null) }} </a>
                                    </td>
                                    <td class="center">{{ Super::getProperty($account->email ?? null) }}</td>
                                    <td class="center">
                                        <a href="{{ route('accounts.show' , $account->id ) }}"> {{ Super::min_address($account->account_name ?? null,30) }} </a>
                                    </td>
                                    <td class="center">{{ Super::getProperty($account->mobile ?? null) }}</td>
                                    <td class="center">{{ Super::getProperty(isset($account->is_active) ? (($account->is_active === 1) ? 'activated' : 'not activated') : null) }}</td>
                                    @if($auth->role_id == $role_rk_super_admin || $auth->role_id == $role_rk_admin)
                                        <td class="center">
                                            <div class="btn-group control-icon">
                                                <a title="{{ trans('lang.edit_account') }}" style="margin:1px"
                                                   href="{{ route('accounts.edit', $account->id)  }}">
                                                    <i
                                                        class="ace-icon fa fa-edit bigger-120  edit"
                                                        data-id="">
                                                    </i>
                                                </a>
                                                @can('super-only', $auth)
                                                    <a title="{{ trans('lang.delete_account') }}" href="#"
                                                       style="margin:1px">
                                                        <i
                                                            class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                            data-id="{{ $account->id }} "
                                                            data-link="{{ route('accounts.destroy', $account->id) }}"
                                                            data-type="DELETE">
                                                        </i>
                                                    </a>

                                                    <a title="{{ trans('lang.access_account') }}" href="{{ route('accounts.access', $account->user_id) }}"
                                                       style="margin:1px">
                                                        <i
                                                            class="ace-icon fa fa-key bigger-120 delete">
                                                        </i>
                                                    </a>
                                                @endcan
                                                @if($account->is_published == 0 || $account->is_published == 2)
                                                    <a href="{{  url('accounts/'.$account->id.'/publish/true')  }}"
                                                       title="{{ trans('lang.publish_account') }}" style="margin:1px">
                                                        <i class="ace-icon fa fa-lg fa-eye bigger-120 edit"
                                                           data-id=""></i>
                                                    </a>
                                                @else
                                                    <a href="{{ url('accounts/'.$account->id.'/publish/false')   }}"
                                                       title="{{ trans('lang.un_publish_account') }}"
                                                       style="margin:1px">
                                                        <i class="ace-icon fa fa-lg fa-eye-slash bigger-120 edit"
                                                           data-id=""></i>
                                                    </a>
                                                @endif
                                                @if($account->is_active == 0)
                                                    <a style="margin:1px"
                                                       title="{{ trans('lang.activate_account') }}"
                                                       href="{{ route('accounts.activate', $account->id)  }}"><i
                                                            class="ace-icon fa fa-check bigger-120 delete"
                                                            data-id=""></i></a>
                                                @endif

                                                {{--  account stages  --}}
                                                <a href="#" data-id="{{ $account->user_id }}" id="account_status"
                                                   title=" account stages">
                                                    <i class="ace-icon fa fa-lg fa-comment bigger-120 delete"></i>
                                                </a>

                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_accounts.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_accounts')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            var URL = "{{ url('/') }}";
            // get the list of cities according to the county_id
            $(document).on('change', '#type', function () {
                console.log(URL);
                window.location = URL + '/accounts?type=' + $(this).val()
            });
        });


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
@stop
