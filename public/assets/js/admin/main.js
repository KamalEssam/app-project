$(document).ready(function () {
    // Setup CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

$(document).on('click', '.navbar-toggle', function () {
    $('.nav-pulled-top').width($(window).width())
    $('.nav-pulled-top').height($(window).height())
})
});