@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_ads') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_ads')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('ads.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($ads) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.day_from')}}</th>
                                <th class="center">{{trans('lang.day_to')}}</th>
                                <th class="center">{{trans('lang.time_from')}}</th>
                                <th class="center">{{trans('lang.time_to')}}</th>
                                <th class="center">{{trans('lang.slide')}}</th>
                                <th class="center">{{trans('lang.priority')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ads as $ad)
                                <tr>
                                    <td class="center">{{ $ad->{app()->getLocale() . '_title'}  }}</td>
                                    <td class="center">{{ $ad->date_from  }}</td>
                                    <td class="center">{{ $ad->date_to  }}</td>
                                    <td class="center">{{ \App\Http\Traits\DateTrait::getTimeByFormat( $ad->time_from, 'h:i a') }}</td>
                                    <td class="center">{{  \App\Http\Traits\DateTrait::getTimeByFormat($ad->time_to, 'h:i a')  }}</td>
                                    <td class="center">{{ $ad->slide  }}</td>
                                    <td class="center">{{ $ad->priority  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('ads.edit', $ad->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_ad') }}"
                                                    data-id=""></i></a>
{{--                                            <a href="{{ route('offers.show', $ad->id)  }}"><i--}}
{{--                                                    class="ace-icon fa fa-eye bigger-120 view"--}}
{{--                                                    title="{{ trans('lang.show_ads') }}"--}}
{{--                                                    data-id=""></i></a>--}}

                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_ad') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $ad->id }} "
                                                        data-link="{{ route('ads.destroy', $ad->id) }}"
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
                                                                src="{{ asset('assets/images/no_data/no_offer.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_ads')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
