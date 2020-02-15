@extends('layouts.admin.admin-master')

@section('title', trans('lang.show_offers'))

@section('styles')
    {!! Html::style('assets/css/admin/colorbox.min.css') !!}
    <style>
        #cboxContent {
            border: 5px solid #000;
        }

        #cboxClose {
            top: -3px;
            background-color: #000;
            border: 2px solid #FFF;
            border-radius: 32px;
            color: #FFF;
            font-size: 21px;
            height: 28px;
            width: 28px;
            padding-bottom: 2px;
            margin-left: 0;
            right: -2px;
        }

        #cboxOverlay {
            background: #000;
        }

        .profile-info-name {
            width: 180px;
            text-align: left;
        }
    </style>
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ $offer->name }}</h1>
                        <hr>

                        <div style="width: 150px; margin: auto">
                            <ul class="ace-thumbnails clearfix">
                                <li style="width: 150px">
                                    <a href="{{ $offer->image }}" data-rel="colorbox">
                                        <img width="150" height="150" alt="150x150" src="{{ $offer->image }}"/>
                                        <div class="text">
                                            <div class="inner">view offer image</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="profile-user-info profile-user-info-striped">
                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{ trans('lang.name') }} </div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="username">{{ $offer->name }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{ trans('lang.description') }} </div>

                                <div class="profile-info-value">
                                    <span class="editable">{{ $offer->desc }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.doctor') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable" id="age">{{ $offer->doctor->name }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.reservation_fees_included') }}</div>

                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="signup">{{ $offer->reservation_fees_included == 1 ? trans('lang.yes') : trans('lang.no') }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.price') }}</div>
                                <div class="profile-info-value">
                                    <span class="editable" id="login"> {{ $offer->price }}
                                        @if ($offer->price_type == 0)
                                            Egp
                                        @else
                                            %
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.user_booked') }}</div>
                                <div class="profile-info-value">
                                    <span class="editable" id="about">{{ $offer->users_booked > 0 ?: 'not set' }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.is_featured') }}</div>
                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="about">{{ $offer->is_featured == 1 ? trans('lang.yes') : trans('lang.no') }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.expiry_date') }}</div>
                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="about">{{ \Carbon\Carbon::parse($offer->expiry_date)->format('Y-m-d') }}</span>
                                </div>
                            </div>

                            <div class="profile-info-row">
                                <div class="profile-info-name">{{ trans('lang.views_no') }}</div>
                                <div class="profile-info-value">
                                    <span class="editable"
                                          id="about">{{ $offer->views_no }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    {!! Html::script('assets/js/admin/jquery.colorbox.min.js') !!}
    <script>
        var $overflow = '';
        let colorbox_params = {
            rel: 'colorbox',
            reposition: true,
            scalePhotos: true,
            scrolling: false,
            previous: '<i class="ace-icon fa fa-arrow-left"></i>',
            next: '<i class="ace-icon fa fa-arrow-right"></i>',
            close: '&times;',
            current: '{current} of {total}',
            maxWidth: '100%',
            maxHeight: '100%',
            onOpen: function () {
                $overflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
            },
            onClosed: function () {
                document.body.style.overflow = $overflow;
            },
            onComplete: function () {
                $.colorbox.resize();
            }
        };
        $('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
    </script>
@stop
