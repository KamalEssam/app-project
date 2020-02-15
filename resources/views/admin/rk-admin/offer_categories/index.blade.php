@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_offer_categories') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_offer_categories')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('offer_categories.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($offerCategories) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center" style="max-width: 65px">{{trans('lang.image')}}</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.offers')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offerCategories as $offerCategory)
                                <tr>
                                    <td class="center"><img src="{{ $offerCategory->image  }}" style="width: 65px !important;height: 65px !important;" alt="{{ $offerCategory->{app()->getLocale() . '_name'} }}"></td>
                                    <td class="center">{{ $offerCategory->{app()->getLocale() . '_name'}  }}</td>
                                    <td class="center">{{ $offerCategory->offers  .' '.  str_plural('offer', $offerCategory->offers)   }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('offer_categories.edit', $offerCategory->id)  }}"><i
                                                        class="ace-icon fa fa-edit bigger-120  edit"
                                                        title="{{ trans('lang.edit_offer_category') }}"></i></a>
{{--                                            <a title="{{ trans('lang.delete_offer_category') }}"--}}
{{--                                                    href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"--}}
{{--                                                           data-id="{{ $offerCategory->id }} "--}}
{{--                                                           data-link="{{ route('offer_categories.destroy', $offerCategory->id) }}"--}}
{{--                                                           data-type="DELETE"></i></a>--}}
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
                                    class="loon no_data">{{trans('lang.no_offer_categories')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop





