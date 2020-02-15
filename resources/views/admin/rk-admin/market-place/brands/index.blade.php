@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_brands') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_brands')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('brands.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($brands) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.image')}}</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.mobile')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td class="center">
                                        @if ($brand->getOriginal('image') == 'default.png')
                                            <img src="{{ asset('assets/images/default.png') }}" alt="image"
                                                 style="width: 80px;height: 90px">
                                        @else
                                            <img src="{{ asset('assets/images/brands/' . $brand->image) }}" alt="image"
                                                 style="width: 80px;height: 90px">
                                        @endif
                                    </td>
                                    <td class="center">{{ $brand->name  }}</td>
                                    <td class="center">{{ $brand->mobile }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_brand') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $brand->id }}"
                                                        data-link="{{ route('brands.destroy', $brand->id) }}"
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
                                                                src="{{ asset('assets/images/no_data/no_sales.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_brands')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop





