@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_reports') )

@section('styles')
    <style>
        .container > .dropdown {
            margin: 0 20px;
            vertical-align: top;
        }

        .dropdown {
            display: inline-block;
            position: relative;
            overflow: hidden;
            height: 28px;
            min-width: 100px;
            margin-left: 10px;
            background: #f2f2f2;
            border: 1px solid;
            border-color: white #f7f7f7 whitesmoke;
            border-radius: 3px;
            background-image: -webkit-linear-gradient(top, transparent, rgba(0, 0, 0, 0.06));
            background-image: -moz-linear-gradient(top, transparent, rgba(0, 0, 0, 0.06));
            background-image: -o-linear-gradient(top, transparent, rgba(0, 0, 0, 0.06));
            background-image: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.06));
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08);
        }

        .dropdown:before, .dropdown:after {
            content: '';
            position: absolute;
            z-index: 2;
            top: 9px;
            right: 10px;
            width: 0;
            height: 0;
            border: 4px dashed;
            border-color: #888888 transparent;
            pointer-events: none;
        }

        .dropdown:before {
            border-bottom-style: solid;
            border-top: none;
        }

        .dropdown:after {
            margin-top: 7px;
            border-top-style: solid;
            border-bottom: none;
        }

        .dropdown-select {
            position: relative;
            width: 130%;
            margin: 0;
            padding: 6px 8px 6px 10px;
            height: 28px;
            line-height: 14px;
            font-size: 12px;
            color: #62717a;
            text-shadow: 0 1px white;
            background: #f2f2f2; /* Fallback for IE 8 */
            background: rgba(0, 0, 0, 0) !important; /* "transparent" doesn't work with Opera */
            border: 0;
            border-radius: 0;
            -webkit-appearance: none;
        }

        .dropdown-select:focus {
            z-index: 3;
            width: 100%;
            color: #394349;
            outline: 2px solid #49aff2;
            outline: 2px solid -webkit-focus-ring-color;
            outline-offset: -2px;
        }

        .dropdown-select > option {
            margin: 3px;
            padding: 6px 8px;
            text-shadow: none;
            background: #f2f2f2;
            border-radius: 3px;
            cursor: pointer;
        }

        /* Fix for IE 8 putting the arrows behind the select element. */

        .lt-ie9 .dropdown {
            z-index: 1;
        }

        .lt-ie9 .dropdown-select {
            z-index: -1;
        }

        .lt-ie9 .dropdown-select:focus {
            z-index: 3;
        }

        /* Dirty fix for Firefox adding padding where it shouldn't. */

        @-moz-document url-prefix() {
            .dropdown-select {
                padding-left: 6px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_reports')}}</h1>
                </div>
                <div class="col-md-1">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                @if(count($reports) > 0)
                    <div class="table-responsive">
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="center">{{trans('lang.reporter')}}</th>
                                <th class="center">{{trans('lang.type')}}</th>
                                <th class="center">{{trans('lang.complaint')}}</th>
                                <th class="center">{{trans('lang.time')}}</th>
                                <th class="center">{{trans('lang.controls')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td class="center">{{ $report->user->name ?? 'deleted' }}</td>
                                    <td class="center">{{ Config::get('reports')[$report->type][app()->getLocale() . '_name'] }}</td>
                                    <td class="center">{{  $report->body  }}</td>
                                    <td class="center">{{  $report->created_at  }}</td>
                                    <td class="center">
                                        <div class="control-icon">
                                            <a href="{{  route('reservations.details',['id' => $report->reservation_id])  }}"><i
                                                    class="ace-icon view fa fa-eye"
                                                    title="show reservation details"></i></a>
                                            <div class="dropdown">
                                                <select name="status" id="status" class="dropdown-select"
                                                        data-id="{{ $report->id }}">
                                                    <option value="0" {{ $report->status == 0 ? 'selected' : ''}}>pending
                                                    </option>
                                                    <option value="1" {{ $report->status == 1 ? 'selected' : ''}}>solved
                                                    </option>
                                                </select>
                                            </div>
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
                                class="loon no_data">{{trans('lang.no_reports')}}</p></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@push('more-scripts')
    <script>
        $(document).on('change', '#status', function (e) {
            let report_id = $(this).data('id');
            let status = $(this).val();

            $.ajax({
                url: URL + '/report/change-status',
                type: 'POST',
                data: {
                    _token: token,
                    id: report_id,
                    status: status
                }
            }).done(function (data) {
                if (data.status == false) {
                    swal({
                        title: "Failure",
                        text: data.msg,
                        type: "warning",
                    });
                }
            });
        });
    </script>
@endpush
