@extends('layouts.admin.admin-master')
@section('title',  trans('lang.manage_specialities') )
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage-sponsored-doctor')}}</h1>
                </div>

                @if(count($doctors) < 5)
                    <div class="col-md-1">
                        <a href="{{ route('sponsored.create', $id ?? 0) }}"
                           class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                    </div>
                @endif

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($doctors) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center hidden"></th>
                                <th class="center">{{trans('lang.doctor')}}</th>
                                <th class="center">{{trans('lang.rank')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @for($i = 0,$iMax = count($doctors);$i < $iMax; $i++)
                                <tr>
                                    <td class="center hidden"></td>
                                    <td class="center">{{ $doctors[$i]->name   }}</td>
                                    <td class="center">

                                        @if ($doctors[$i]->featured_rank != 5)
                                            <a href="{{ route('sponsored.rank',[$doctors[$i],1]) }}"
                                               title="{{ trans('lang.top') }}"><i
                                                    class="ace-icon fas fa-chevron-up bigger-120"></i>
                                            </a>
                                        @endif
                                            <p class="ml-10 mr-10">
                                                @php
                                                    switch ( $doctors[$i]->featured_rank) {
                                                        case 1:
                                                            echo 5;
                                                            break;
                                                        case 2:
                                                            echo 4;
                                                            break;
                                                        case 4:
                                                            echo 2;
                                                            break;
                                                        case 5:
                                                            echo 1;
                                                            break;
                                                        default:
                                                            echo  $doctors[$i]->featured_rank;
                                                    }
                                                @endphp
                                            </p>

                                        @if ($doctors[$i]->featured_rank != 1)
                                            <a href="{{ route('sponsored.rank',[$doctors[$i],0]) }}"
                                               title="{{ trans('lang.down') }}"><i
                                                    class="ace-icon fas fa-chevron-down bigger-120"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="#"
                                               title="{{ trans('lang.remove_sponsor') }}"><i
                                                    class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                    data-id="{{  $doctors[$i]->id }} "
                                                    data-link="{{ route('sponsored.remove',  $doctors[$i]->id) }}"
                                                    data-type="DELETE"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row">
                        <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                src="{{ asset('assets/images/no_data/no_specialities.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_specialities')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop





