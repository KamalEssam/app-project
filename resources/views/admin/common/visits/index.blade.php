@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_visits'))

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-3">
                    <h1>{{ trans('lang.manage_visits') }}</h1>
                </div>

                <div class="col-md-3 col-xs-12" style="position: relative">
                    <input type="text" value=""
                           class="day day-media form-control date"
                           placeholder="{{ trans('lang.search_by_reservation_date') }}"
                           id="search">
                </div>

                <div class="col-md-3 col-xs-12">
                    <input type="text" class="search form-control day-media "
                           placeholder="{{ trans('lang.search_by_username') }}"
                           name="search">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>

                <div id="table-visits">
                    @if(count($visits) > 0)
                        <div class="table-responsive">
                            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="center">{{trans('lang.name')}}</th>
                                    <th class="center">{{trans('lang.type')}}</th>
                                    <th class="center">{{trans('lang.reservation_date')}}</th>
                                    <th class="center">{{trans('lang.clinic_name')}}</th>
                                    <th class="center">{{trans('lang.next_visit')}}</th>
                                    @if(app()->user()->role_id == $role_doctor)
                                        <th class="center">{{trans('lang.created_by')}}</th>
                                        <th class="center">{{trans('lang.updated_by')}}</th>
                                    @endif
                                    <th class="center">{{trans('lang.controls')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($visits as $visit)
                                    <tr>
                                        <td class="center control-icon">
                                            {{ ($visit->patient_name)}}
                                        </td>
                                        <td class="center">
                                            <p> {{ $visit->type == 0 ? trans('lang.check_up') : trans('lang.follow_up')}}</p>
                                        </td>
                                        <td class="center">{{ Super::getProperty( $visit->reservation->day) }}</td>

                                        <td class="center">{{ Super::getProperty( $visit->clinic_name) }}</td>

                                        <td class="center">{{ Super::getProperty( $visit->next_visit) }}</td>

                                        @if(app()->user()->role_id == $role_doctor)
                                            <td class="center">{{ $visit->createdBy->name ?? trans('lang.n/a')  }}</td>
                                            <td class="center">{{ $visit->updatedBy->name ?? trans('lang.n/a') }}</td>
                                        @endif
                                        <td class="center">

                                            <div class="btn-group control-icon">
                                                <a href="{{route('visits.show' , [$visit->user_id])}}">
                                                    <i class="add fa fa-eye"></i>
                                                </a>
                                                @if(app()->user()->role_id == $role_doctor)
                                                    <a href="{{ route('visits.edit', $visit->id)  }}">
                                                        <i class="ace-icon fa fa-edit bigger-120  edit"></i>
                                                    </a>
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
                                                                    src="{{ asset('assets/images/no_data/no_visits.png') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center"><p
                                        class="loon no_data">{{trans('lang.no_visits')}}</p></div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

@stop

@section('scripts')

    <script type="text/javascript">
        $(document).ready(function () {
            var URL = "{{ url('/') }}";

            var daySelector = $('.day');
            var searchSelector = $('.search');

            var day = '';
            var name = '';

            searchSelector.keyup(function () {
                day = daySelector.val() == '' ? 'none' : daySelector.val();
                name = searchSelector.val() == '' ? 'none' : searchSelector.val();
                $('#table-visits').load(URL + '/visit/table-visits/' + day + '/' + name);
            });

            daySelector.on('change', function () {
                day = daySelector.val() == '' ? 'none' : daySelector.val();
                name = searchSelector.val() == '' ? 'none' : searchSelector.val();
                $('#table-visits').load(URL + '/visit/table-visits/' + day + '/' + name);
            });
        });
    </script>

@stop