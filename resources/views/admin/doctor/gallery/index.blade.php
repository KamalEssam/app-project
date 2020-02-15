@extends('layouts.admin.admin-master')

@section('title',  trans('lang.manage_gallery') )

@section('styles')
    {!! Html::style('assets/css/lightbox.min.css') !!}
    <style>
        .image-container {
            background-color: #EEE;
            text-align: center;
            padding: 10px
        }

        .link-size {
            display: block;
            width: 100%;
            height: 186px;
        }

        .img-container-box {
            box-shadow: 0 5px 20px rgba(128, 128, 128, 0.28);
            margin-bottom: 30px
        }
    </style>
@stop
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage_gallery')}}</h1>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('gallery.create') }}"
                       class="btn btn-sm btn-primary btn-block btn-add">{{ trans('lang.add') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="container">
                    <div class="clearfix">
                        <div class="pull-right tableTools-container"></div>
                    </div>
                    @php  $image_count = count($gallery); @endphp
                    <div class="row item-wrapper">
                        @if($image_count > 0)
                            <div class="row">
                                @foreach($gallery as $photo)
                                    <div class="col-md-3 img-container-box">
                                        <div class="relative no-padding"
                                             style="box-shadow: 0 5px 20px rgba(128,128,128, 0.28);margin-bottom: 30px">
                                            <a class="link-size"
                                               href="{{ $photo->image }}" data-lightbox="roadtrip">
                                                <div class="thumbnail"
                                                     style='width: 100%; height: 100%; background-size: cover;background-repeat: no-repeat;background-image: url({{ $photo->image }})'></div>
                                            </a>
                                            <div class="image-control image-container">
                                                <div class="btn-group control-icon">
                                                    <a href="#" class="btn-group control-icon"><i
                                                            class="ace-icon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                            data-id="{{$photo->id}} "
                                                            data-link="{{route('gallery.destroy', $photo->id)}}"
                                                            data-type="DELETE"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="row">
                                <div class="col-xs-12 text-center"><img class="no_data_image" alt="{{trans('lang.no_gallery')}}"
                                                                        src="{{ asset('assets/images/no_data/no_photos.png') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center"><p
                                        class="loon no_data">{{trans('lang.no_gallery')}}</p></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('more-scripts')
    {!! Html::script('assets/js/lightbox.min.js') !!}
    <script type="text/javascript">
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'fitImagesInViewport': 100
        })
    </script>
@endpush
