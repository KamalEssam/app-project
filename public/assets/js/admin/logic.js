$(document).ready(function () {
    var i = 0;
    $('.add-experience').on('click', function (e) {
        e.preventDefault();
        i++;
        $('.experiences').append(
            '<hr>' +
            '<div class="row">' +
            '<div class="form-group col-md-6 has-float-label">' +
            '<input type="text" name="company[]" ' + 'class="form-control" id="company' + i + '" placeholder="Company" required>' +
            '<label for="compnay' + i + '">Company<span class="astric">*</span></label>' +
            '</div>' +

            '<div class="form-group col-md-6 has-float-label">' +
            '<input type="text" name="job_title[]" ' + 'class="form-control" id="job_title' + i + '" placeholder="Job Title" required>' +
            '<label for="job_title' + i + '">Job Title<span class="astric">*</span></label>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="form-group col-md-12 has-float-label">' +
            '<input type="date" name="start_date[]" ' + 'class="form-control" id="start_date' + i + '" placeholder="Start Date" required>' +
            '<label for="start_date' + i + '">Start Date<span class="astric">*</span></label>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="form-group col-md-12">' +
            '<input type="checkbox" name="is_present' + i + '" id="is-present' + i + '" class="is-present" style="height: 13px!important;">' +
            '<label for="is_present' + i + '">&nbsp; Present</label>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="form-group col-md-12 has-float-label">' +
            '<input type="date" name="end_date[]" ' + 'class="form-control" id="end_date' + i + '" placeholder="End Date" required>' +
            '<label for="end_date' + i + '">End Date<span class="astric">*</span></label>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="form-group col-md-12 has-float-label">' +
            '<input type="text" name="salary[]" ' + 'class="form-control" id="salary' + i + '" placeholder="Start Date" required>' +
            '<label for="salary' + i + '">Salary<span class="astric">*</span></label>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="form-group col-md-12 has-float-label">' +
            '<input type="text" name="reason[]" ' + 'class="form-control" id="reason' + i + '" placeholder="Leaving Reason" required>' +
            '<label for="reason' + i + '">Leaving Reason<span class="astric">*</span></label>' +
            '</div>' +
            '</div>'
        );
    });


    $(document).on('click', '.is-present', function (e) {
        if (this.checked) {
            $(e.target).parent().parent().next().hide().find('input').removeAttr('required');
            $(e.target).parent().parent().next().next().hide().find('input').removeAttr('required');
        } else {
            $(e.target).parent().parent().next().show().find('input').attr('required', 'required');
            $(e.target).parent().parent().next().next().show().find('input').attr('required', 'required');
        }
    })
    $(document).on('click', '.is-fresh', function (e) {
        if (this.checked) {
            $(e.target).parent().parent().nextAll().hide().find('input').removeAttr('required');
            $('#add-more').hide();
        } else {
            $(e.target).parent().parent().nextAll().show().find('input').attr('required', 'required');
            $('#add-more').show();

        }

    })
});