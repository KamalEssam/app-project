<div class="form-group col-md-12">
    <div class="row">
        <div class="col-md-2 label-form">
            <label for="city_id">{{ trans('lang.city') }} <span class="astric">*</span></label>
        </div>
        <div class="col-md-12">
            <select name="city_id" id="city_id" class='form-control {{ ($errors->has('city_id') ? 'redborder' : '') }}'>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->en_name }}</option>
                @endforeach
            </select>
            <small class="text-danger">{{ $errors->first('city_id') }}</small>
        </div>
    </div>
</div>
