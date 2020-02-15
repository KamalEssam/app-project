<div id="modal" data-iziModal-title="{{ trans('lang.add_user') }}"
     data-iziModal-subtitle="{{ trans('lang.add_user_sub') }}" data-iziModal-icon="icon-home">

    <form id="modal-form" action="{{ route('patients.store') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-2 label-form">
                        <label for="name">{{trans('lang.name')}}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::text('name', null, ['class'=>' form-control ' . ($errors->has('name') ? 'redborder' : '')  ,'required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('name') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-2 label-form">
                        <label for="email">{{trans('lang.email')}} <span class="astric">*</span></label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::email('email', null, ['class'=>'form-control ' . ($errors->has('email') ? 'redborder' : '')  , 'required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('email') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-2 label-form">
                        <label for="mobile">{{trans('lang.mobile')}}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::text('mobile', null, ['pattern' => '(01)[0-9]{9}' ,'min'=>11, 'class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : '')  ,'required' => 'required', 'id'=>'mobile-validate']) }}
                        <small class="text-danger">{{ $errors->first('mobile') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-2 label-form">
                        <label for="type"> {{trans('lang.gender')}}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::select('gender', [0 => 'male', 1 => 'female'], null, ['class'=>'form-control ' . ($errors->has('gender') ? 'redborder' : '') ,'required' => 'required', 'id'=>'gender']) }}
                        <small class="text-danger">{{ $errors->first('gender') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-2 label-form">
                        <label for="address">{{trans('lang.address')}}</label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::textarea('address', null, ['class'=>'form-control ' . ($errors->has('address') ? 'redborder' : '')  , 'id'=>'address',  'rows'=>'3']) }}
                        <small class="text-danger">{{ $errors->first('address') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <input type="submit" value="{{ trans('lang.add_user') }}"
               class="btn-modal-form-submit btn btn-primary btn-xs pull-right" id="submit-patient">
        </div>
    </form>
    <div class="container" id="loading" style="display: none; !important;">
        <div id="overlay" class="open">
            <div class="display-loading open"></div>
        </div>
    </div>
</div>

