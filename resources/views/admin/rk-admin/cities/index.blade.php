@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_cities') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_cities')}}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('cities.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($cities) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.country')}}</th>
                                <th class="center">{{ trans('lang.created_by') }}</th>
                                <th class="center">{{ trans('lang.updated_by') }}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($cities as $city)
                                <?php
                                $created_by = \App\Models\User::where('id', $city->created_by)->first();
                                $updated_by = \App\Models\User::where('id', $city->updated_by)->first();

                                ?>
                                <tr>
                                    <td class="center">{{ $city[app()->getLocale() . '_name']  }}</td>
                                    <td class="center">{{ $city->country[app()->getLocale() . '_name'] }}</td>
                                    <td class="center">{{ Request::is( isset($created_by) ) ? $created_by->name : trans('lang.n/a')  }}</td>
                                    <td class="center">{{ Request::is( isset($updated_by) ) ? $updated_by->name : trans('lang.n/a')  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('cities.edit', $city->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_city') }}"
                                                    data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_city') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $city->id }} "
                                                        data-link="{{ route('cities.destroy', $city->id) }}"
                                                        data-type="DELETE"></i></a>
                                            @endcan
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
                                                                src="{{ asset('assets/images/no_data/no_cities.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_cities')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop





