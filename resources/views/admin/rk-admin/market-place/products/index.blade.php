@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_products') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_products')}}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('product.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($products) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{ trans('lang.image') }}</th>
                                <th class="center">{{trans('lang.title')}}</th>
                                <th class="center">{{trans('lang.description')}}</th>
                                <th class="center">{{ trans('lang.price') }}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td class="center">
                                        @if ($product->getOriginal('image') == 'default.png')
                                            <img src="{{ asset('assets/images/default.png') }}" alt="image"
                                                 style="width: 80px;height: 90px">
                                        @else
                                            <img src="{{ $product->image }}" alt="image"
                                                 style="width: 80px;height: 90px">
                                        @endif
                                    </td>
                                    <td class="center">{{ $product[app()->getLocale() . '_title'] }}</td>
                                    <td class="center">{{ $product[app()->getLocale() . '_desc'] }}</td>
                                    <td class="center">{{ $product->price }} {{ trans('lang.egp')  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('product.edit', $product->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_product') }}"
                                                    data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_product') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $product->id }} "
                                                        data-link="{{ route('product.destroy', $product->id) }}"
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
                        <div class="col-xs-12 text-center"><img class="no_data_image" alt="no products"
                                                                src="{{ asset('assets/images/no_data/no_category.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_products')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop





