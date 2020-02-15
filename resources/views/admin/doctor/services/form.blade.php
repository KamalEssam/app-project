@php
    if ($auth->is_premium == 1) {
        $columns = [4,3,3];
     } else {
        $columns = [6,4];
     }
@endphp
<div class="fields" id="other">
    <div class="add-new-service">
        <div class="form-group col-md-{{$columns[0]}}" style="padding-left: 0px;">
            {{ Form::select('service_id[]',$services_list, null,[ 'class'=>'chosen-select form-control' . ($errors->has('en_name') ? 'redborder' : '')  , 'id'=>'en_name[] form-field-select-2', 'pattern' => $english_regex,'required' => 'required','title' => trans('lang.only_english')]) }}
            <small class="text-danger">{{ $errors->first('en_name[]') }}</small>
        </div>
        <div class="form-group col-md-{{$columns[1]}}">
            <input type="number" name="price[]" id="price" class="form-control" placeholder="price" step="0.01" min="0"
                   required>
        </div>

        @if (isset($columns[2]))
            <div class="form-group col-md-{{ $columns[2] }}">
                <input type="number" name="premium_price[]" id="premium_price" class="form-control" step="0.01" required
                       min="0"
                       placeholder="premium price">
            </div>
        @endif
    </div>
    <div class="col-md-2" style="margin-bottom: 30px">
        <a class="btn btn-primary btn-xs add-other"><i class="fa fa-plus"></i></a>
    </div>
</div>
{{  Form::submit($btn , ['class' => 'btn-loon mt-20 ' . $classes ]) }}


@push('more-scripts')
    <script>
        $(".chosen-select").chosen('');
        $('.add-other').click(function () {
            // var oth = $('.add-new-service').html();
            var final = "<div>" +
                "<div class=\"form-group col-md-{{$columns[0]}}\" style=\"padding-left: 0px;\">\n" +
                '            {{ Form::select('service_id[]',$services_list, null,[ 'class'=>'chosen-select form-control' . ($errors->has('en_name') ? 'redborder' : '')  , 'id'=>'en_name[] form-field-select-2','required' => 'required','title' => trans('lang.only_english')]) }}\n' +
                "            <small class=\"text-danger\">{{ $errors->first('en_name[]') }}</small>\n" +
                "        </div>" +
                "    <div class=\"form-group col-md-{{$columns[1]}}\">\n" +
                "            <input type=\"number\" name=\"price[]\" id=\"price\" class=\"form-control\" placeholder=\"price\" step=\"0.01\" min=\"0\"  required>\n" +
                "        </div>\n"
                @if (isset($columns[2]))
                + "        <div class=\"form-group col-md-{{$columns[2]}}\">\n" +
                "            <input type=\"number\" name=\"premium_price[]\" id=\"premium_price\"  min=\"0\"  class=\"form-control\"\n" +
                "                   placeholder=\"premium price\"  step=\"0.01\"  required>\n" +
                "        </div>"
                @endif
                + "<div class='col-md-1'><a class='btn btn-danger btn-xs del-other' ><i class='fa fa-trash'></i></a>" +
                "</div>";
            $('#other').append(final);
            $(".chosen-select").chosen('');
        });
        $(document).on('click', '.del-other', function () {
            $(this).parent().parent().html('');
        });
    </script>
@endpush
