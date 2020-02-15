<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-3 label-form">
                <label for="old">{{trans('lang.old_password')}} <span class="astric">*</span> </label>
            </div>
            <div class="col-md-9">
                {{  Form::password('old', [ 'class' => 'form-control ' . ($errors->has('old') ? 'redborder' : ''),'id' => 'old_password' ]) }}
                <small class="text-danger">{{ $errors->first('old') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-3 label-form">
                <label for="new">{{trans('lang.new_password')}}  <span class="astric">*</span></label>
            </div>
            <div class="col-md-9">
                {{  Form::password('new', [ 'class' => 'form-control ' . ($errors->has('new') ? 'redborder' : '') ]) }}
                <small class="text-danger">{{ $errors->has('new') ? $errors->first('new') : '' }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-3 label-form">
                <label for="confirm">{{trans('lang.confirm_password')}}<span class="astric">*</span></label>
            </div>
            <div class="col-md-9">        {{  Form::password('confirm', ['class' => 'form-control ' . ($errors->has('confirm') ? 'redborder' : '') ]) }}
                <small class="text-danger">{{ $errors->has('confirm') ? $errors->first('confirm') : '' }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/admin/profile')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit('Save' , ['class' => 'btn-loon btn-xs pull-right']) }}