@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_apps') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_apps')}}</h1>
                </div>

                <div class="col-md-1">
                    @if(Auth::user()->role_id  ==  5)
                        <a href="{{ route('apps.create') }}"
                           class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($apps) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{ trans('lang.ios') }}</th>
                                <th class="center">{{ trans('lang.android') }}</th>
                                <th class="center">{{ trans('lang.created_by') }}</th>
                                <th class="center">{{ trans('lang.updated_by') }}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($apps as $app)
                                <?php
                                $created_by = \App\Models\User::where('id', $app->created_by)->first();
                                $updated_by = \App\Models\User::where('id', $app->updated_by)->first();
                                ?>
                                <tr>
                                    <td class="center">{{ $app->account->users->where('role_id', 1)->first()['name'] }}</td>
                                    <td class="center"><a target="_blank" href="{{ $app->ios }}"><img
                                                    src="{{ asset('assets/images/logo/ios-logo.png') }}"
                                                    style="width: 22%"></a></td>
                                    <td class="center"><a target="_blank" href="{{ $app->android }}"><img
                                                    src="{{ asset('assets/images/logo/android-logo.png') }}"
                                                    style="width: 22%"></a></td>
                                    <td class="center">{{ Request::is( isset($created_by) ) ? $created_by->name : trans('lang.n/a')  }}</td>
                                    <td class="center">{{ Request::is( isset($updated_by) ) ? $updated_by->name : trans('lang.n/a')  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                           data-id="{{ $app->id }} "
                                                           data-link="{{ route('apps.destroy', $app->id) }}"
                                                           data-type="DELETE"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">{{trans('lang.no_apps')}}</div>
                @endif
            </div>
        </div>
    </div>

@stop






