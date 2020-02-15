@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_categories') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_categories')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('category.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($categories) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.image')}}</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.is_active')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td class="center">
                                        @if ($category->getOriginal('image') == 'default.png')
                                            <img src="{{ asset('assets/images/default.png') }}" alt="image"
                                                 style="width: 80px;height: 90px">
                                        @else
                                            <img
                                                src="{{ $category->image }}"
                                                alt="image"
                                                style="width: 80px;height: 90px">
                                        @endif
                                    </td>
                                    <td class="center">{{ $category->{app()->getLocale() . '_name'}  }}</td>
                                    <td class="center">{{ $category->is_active == 1 ? 'Yes' : 'No' }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('category.edit', $category->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_category') }}"
                                                    data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_brand') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $category->id }}"
                                                        data-link="{{ route('category.destroy', $category->id) }}"
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
                                                                src="{{ asset('assets/images/no_data/no_category.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_categories')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
