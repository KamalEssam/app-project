$(document).ready(function(){
    $('.generate-password').on('click', function (e) {
        e.preventDefault();
        var randomstring = Math.random().toString(36).slice(-8);
        $('.generated-passowrd').text(randomstring);
    });
});