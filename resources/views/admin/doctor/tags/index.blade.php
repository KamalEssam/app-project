@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_tags') )


@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_tags')}}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('tags.create') }}" class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                @if(count($tags) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                                @foreach($tags as $tag)
                                    <tr>
                                        <td class="center">{{ $tag[ App::getLocale() . '_name' ] }}</td>
                                        <td class="center">
                                            <div class="btn-group control-icon">
                                                <a href="{{route('tags.edit', $tag->id)}}"><i class="ace-icon fa fa-edit bigger-120  edit" data-id=""></i></a>
                                                <a href="#"><i class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"  data-id="{{$tag->id}} " data-link="{{route('tags.destroy', $tag->id)}}" data-type="DELETE"></i></a>
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
                                                                src="{{ asset('assets/images/no_data/no_tags.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_tags')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop





