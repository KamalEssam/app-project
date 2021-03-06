<div class="fields" id="other">
    <div class="add-new-service">
        <div class="form-group col-md-5" style="padding-left: 0px;">
            <input value="" name="en_name[]" type="text"
                   pattern="{{ $english_regex }}"
                   title="{{trans('lang.only_english')}}"
                   class="form-control {{($errors->has('en_name[]') ? 'redborder' : '')}}"
                   id="en_name[]" placeholder="{{trans('lang.en_name')}}" required>
            <small class="text-danger">{{ $errors->first('en_name[]') }}</small>
        </div>
        <div class="form-group col-md-5">
            <input value="" name="ar_name[]" type="text"
                   pattern="{{ $arabic_regex }}"
                   title="{{ trans('lang.only_arabic') }}"
                   class="form-control {{($errors->has('en_name[]') ? 'redborder' : '')}}"
                   id="ar_name[]" placeholder="{{trans('lang.ar_name')}}" required>
            <small class="text-danger">{{ $errors->first('ar_name[]') }}</small>
        </div>
    </div>
    <div class="col-md-2" style="margin-bottom: 30px">
        <a class="btn btn-primary btn-xs add-other"><i class="fa fa-plus"></i></a>
    </div>
</div>
{{  Form::submit($btn , ['class' => 'btn-loon mt-20 ' . $classes ]) }}

<script>
    $('.add-other').click(function () {
        var oth = $('.add-new-service').html();
        var final = "<div>" + oth + "<div class='col-md-1'><a class='btn btn-danger btn-xs del-other' ><i class='fa fa-trash'></i></a>" +
            "</div>";
        $('#other').append(final);
    });
    $(document).on('click', '.del-other', function () {
        $(this).parent().parent().html('');
    });
</script>