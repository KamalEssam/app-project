@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_offers') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_offers')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('offers.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($offers) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.category')}}</th>
                                <th class="center">{{trans('lang.description')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td class="center">{{ $offer->{app()->getLocale() . '_name'}  }}</td>
                                    <td class="center">{{ $offer->category->name ?? trans('lang.n/a')  }}</td>
                                    <td class="center">{{ Super::min_address($offer->{app()->getLocale() . '_desc'},50) }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('offers.edit', $offer->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_offer') }}"
                                                    data-id=""></i></a>
                                            <a href="{{ route('offers.show', $offer->id)  }}"><i
                                                    class="ace-icon fa fa-eye bigger-120 view"
                                                    title="{{ trans('lang.show_offers') }}"
                                                    data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a title="{{ trans('lang.delete_offer') }}"
                                                   href="#"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $offer->id }} "
                                                        data-link="{{ route('offers.destroy', $offer->id) }}"
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
                                class="loon no_data">{{trans('lang.no_offers')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop





