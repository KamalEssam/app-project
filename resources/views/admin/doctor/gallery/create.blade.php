@extends('layouts.admin.admin-master')

@section('title', trans('lang.add_images'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.add_images')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'gallery.store', 'files' => true]) !!}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12 label-form">
                                        <label for="image"> {{ trans('lang.image') }}</label>
                                    </div>
                                    <div class="col-md-12 form-input">
                                        {{ Form::file('image[]', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'upload_image','multiple' => true]) }}
                                        <small class="text-danger">{{ $errors->first('image') }}</small>
                                        <p class="help-block red"><b>Note:</b> please the max number of images uploaded
                                            by once is 10 images </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{  Form::submit('add' , ['class' => 'btn-loon btn-xs pull-right' ,'id' => 'add_galley']) }}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('more-scripts')
    <script>
        $("#add_galley").on('click', function (e) {
            let $fileUpload = $("input[type='file']");
            if (parseInt($fileUpload.get(0).files.length) > 10) {
                e.preventDefault();
                swal('failure', 'you cant upload more than 10 image at once')
            }
        });
    </script>
@endpush
