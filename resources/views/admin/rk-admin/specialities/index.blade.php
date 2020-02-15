@extends('layouts.admin.admin-master')
@section('title',  trans('lang.manage_specialities') )
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_specialities')}}</h1>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('specialities.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
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
                                <th class="center">{{trans('lang.image')}}</th>
                                <th class="center">{{trans('lang.name')}}</th>
                                <th class="center">{{trans('lang.doctors')}}</th>
                                <th class="center">{{trans('lang.sub_specialities')}}</th>
                                <th class="center">{{trans('lang.services')}}</th>
                                <th class="center">{{trans('lang.featured')}}</th>
                                <th class="center">{{ trans('lang.controls') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($specialities as $speciality)
                                <tr>
                                    <td class="center" style="height: 80px;width: 60px;padding: 2px">
                                        <img src="{{ $speciality->image}}"
                                             alt="{{ $speciality->en_speciality . ' ( ' . $speciality->ar_speciality .  ' ) '  }}">
                                    </td>
                                    <td class="center">{{ $speciality->en_speciality . ' ( ' . $speciality->ar_speciality .  ' ) '  }}</td>
                                    <td class="center">{{$speciality->doctors  .' '.  str_plural('doctor', $speciality->doctors) }}</td>
                                    <td class="center">
                                        <a href="#"
                                           class="btn btn-primary show-specialities"
                                           data-iziModal-open="#modal" style="cursor: pointer;"
                                           data-id="{{ $speciality->id }}">{{ trans('lang.sub_specialities') }}</a>

                                        <div class="btn-group control-icon">
                                            <a href="{{ route('specialities.sub-create', $speciality->id)  }}"><i
                                                    class="ace-icon fa fa-plus bigger-120  edit"
                                                    title="{{ trans('lang.add_sub_speciality') }}"
                                                    data-id=""></i></a>
                                        </div>
                                    </td>

                                    <td class="center">
                                        <a href="#"
                                           class="btn btn-primary show-services"
                                           data-iziModal-open="#modal" style="cursor: pointer;"
                                           data-id="{{ $speciality->id }}">{{ trans('lang.services') }}</a>

                                        <div class="btn-group control-icon">
                                            <a href="{{ route('services.add', $speciality->id)  }}"><i
                                                    class="ace-icon fa fa-plus bigger-120  edit"
                                                    title="{{ trans('lang.create_service') }}"
                                                    data-id=""></i></a>
                                        </div>
                                    </td>

                                    <td class="center">
                                        <a title="{{ trans('lang.is_featured') }}"
                                           href="{{ ($speciality->is_featured == 1) ? route('specialities.featured',['id'=>$speciality->id,'status'=> 0]) : route('specialities.featured',['id'=>$speciality->id,'status'=> 1]) }}">
                                            <i class="{{ $speciality->is_featured == 1 ? 'fas fa-lg fa-star  featured' : 'far fa-lg fa-star '  }}"></i>
                                        </a>
                                    </td>

                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a href="{{ route('specialities.edit', $speciality->id)  }}"><i
                                                    class="ace-icon fa fa-edit bigger-120  edit"
                                                    title="{{ trans('lang.edit_speciality') }}"
                                                    data-id=""></i></a>

                                            @can('super-only', $auth)
                                                <a href="#"
                                                   title="{{ trans('lang.delete_speciality') }}"><i
                                                        class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                        data-id="{{ $speciality->id }} "
                                                        data-link="{{ route('specialities.destroy', $speciality->id) }}"
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
                                                                src="{{ asset('assets/images/no_data/no_specialities.png') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center"><p
                                class="loon no_data">{{trans('lang.no_specialities')}}</p></div>
                    </div>
                @endif
                <div id="modal" data-iziModal-title="{{ trans('lang.specialities') }}"
                     data-iziModal-subtitle="{{ trans('lang.edit') }}" data-iziModal-icon="icon-home">
                    <table class="table table-responsive table-bordered text-center">
                        <thead>
                        <td>{{ trans('lang.name') }}</td>
                        <td>{{ trans('lang.controls') }}</td>
                        </thead>
                        <tbody class="table-content">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@push('more-scripts')
    <script>
        URL = "{{ url('/') }}";
        $(document).on('click', '.show-specialities', function (e) {
            e.preventDefault();
            $.ajax({
                url: URL + '/specialities/sub-speciality/all',
                type: 'POST',
                data: {
                    _token: token,
                    speciality_id: $(this).data('id')
                }
            }).done(function (data) {
                let specialtiesList = '';
                let speciality;
                for (speciality in data) {
                    specialtiesList += "<tr><td>" + data[speciality]['name'] + "<td>" +
                        "<div class='btn-group control-icon'>" +

                        "<a href='" + URL + '/sub-specialities/' + data[speciality]['id'] + '/edit' + "'" +
                        "title=" + "{{ trans('lang.delete_speciality') }}" + "><i class='ace-icon fa fa-edit bigger-120  edit'" +
                        "></i></a> " +

                        "<a href='#'" +
                        "title=" + "{{ trans('lang.delete_speciality') }}" + "><i class='ace-icon fa fa-trash-alt bigger-120 delete ajax-btn'" +
                        "data-id='" + data[speciality]['id'] + "'" +
                        "data-link='" + URL + '/sub-specialities/' + data[speciality]['id'] + "' " +
                        "data-type='DELETE'></i></a>" +
                        "</div></td></tr>";
                }
                $("#modal").iziModal('setSubtitle', "{{trans('lang.sub_specialities')}}");
                $('.table-content').html(specialtiesList);
            });
        });

        $(document).on('click', '.show-services', function (e) {
            e.preventDefault();
            $.ajax({
                url: URL + '/specialities/services/all',
                type: 'POST',
                data: {
                    _token: token,
                    speciality_id: $(this).data('id')
                }
            }).done(function (data) {
                let servicesList = '';
                let service;
                for (service in data) {
                    servicesList += "<tr><td>" + data[service]['name'] + "<td>" +
                        "<div class='btn-group control-icon'>" +

                        "<a href='" + URL + '/services/' + data[service]['id'] + '/edit' + "'" +
                        "title=" + "{{ trans('lang.delete_speciality') }}" + "><i class='ace-icon fa fa-edit bigger-120  edit'" +
                        "></i></a> " +

                        "<a href='#'" +
                        "title=" + "{{ trans('lang.delete_service') }}" + "><i class='ace-icon fa fa-trash-alt bigger-120 delete ajax-btn'" +
                        "data-id='" + data[service]['id'] + "'" +
                        "data-link='" + URL + '/services/' + data[service]['id'] + "'" +
                        "data-type='DELETE'></i></a>" +
                        "</div></td></tr>";
                }
                $("#modal").iziModal('setSubtitle', "{{trans('lang.services')}}");
                $('.table-content').html(servicesList);
            });
        });
    </script>
@endpush





