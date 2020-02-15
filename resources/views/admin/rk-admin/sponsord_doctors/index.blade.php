@extends('layouts.admin.admin-master')
@section('title',  trans('lang.manage_specialities') )
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage-sponsored-doctor')}}</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($specialities) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.doctors')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($specialities as $speciality)
                                <tr>
                                    <td class="center">{{ $speciality->en_speciality . ' ( ' . $speciality->ar_speciality .  ' ) '  }}</td>
                                    <td class="center">{{$speciality->doctors  .' '.  str_plural('doctor', $speciality->doctors) }}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            @if ($speciality->doctors < 5)
                                                <a href="{{ route('sponsored.create', $speciality->id)  }}"><i
                                                        class="ace-icon fa fa-plus bigger-120  edit"
                                                        title="{{ trans('lang.add-sponsored-doctor') }}"
                                                        data-id=""></i></a>
                                            @endif
                                            @if ($speciality->doctors > 0)
                                                <a href="{{ route('sponsored.doctors', $speciality->id)  }}"><i
                                                        class="ace-icon fa fa-eye bigger-120 view"
                                                        title="{{ trans('lang.all-doctors') }}"
                                                        data-id=""></i></a>
                                            @endif
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
@push('more-scripts')
    <script>
    </script>
@endpush





