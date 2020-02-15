@extends('layouts.admin.admin-master')

@section('title', trans('lang.policies'))

@section('styles')
@stop

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{ trans('lang.terms') }}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('policies.create') }}" class="btn btn-sm btn-primary">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($policies) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.type')}}</th>
                                <th class="center">{{trans('lang.condition')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($policies as $policy)
                                <tr>
                                    <td class="center">{{ $policy->type == 1 ? "Term" : "Policy" }}</td>
                                    <td class="center">{{ Super::shortenText($policy[app()->getLocale() . '_condition'],50) }}</td>

                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a title="{{ trans('lang.edit_policy') }}" href="{{ route('policies.edit', $policy->id) }}"><i class="ace-icon fa fa-edit bigger-120  edit" data-id=""></i></a>
                                            <a href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"  data-id="{{ $policy->id }} " data-link="{{route('policies.destroy', $policy->id)}}" data-type="DELETE"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_policies.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_policies')}}</p></div>
                    </div>

                @endif
            </div>
        </div>
    </div>

@stop