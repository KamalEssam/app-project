@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_promos') )

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_promos')}}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('promo-code.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($promos) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.code')}}</th>
                                <th class="center">{{trans('lang.discount')}}</th>
                                <th class="center">{{ trans('lang.infuencer') }}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($promos as $promo)
                                <tr>
                                    <td class="center">{{ $promo->code  }}</td>
                                    <td class="center">{{ $promo->discount . ($promo->discount_type ==  0 ? ' egp' : ' %' ) }}</td>
                                    <td class="center">{{ $promo->infuencer->{app()->getLocale() . '_name'}  }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('promo-code.edit', $promo->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_city') }}"
                                                    data-id=""></i></a>
                                            <a title="{{ trans('lang.delete_city') }}"
                                               href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                           data-id="{{ $promo->id }} "
                                                           data-link="{{ route('promo-code.destroy', $promo->id) }}"
                                                           data-type="DELETE"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_promo-code.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_promos')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>
@stop
