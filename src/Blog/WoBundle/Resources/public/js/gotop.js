$(function() {
    $('.wo-go-top').hide();
    $(window).scroll(function() {
        if ($(window).scrollTop() > 1000)
        {
            $('.wo-go-top').show();
        }
        else
        {
            $('.wo-go-top').hide();
        }
    });
    $('.wo-go-top').click(function() {
        $('html, body').animate({scrollTop: 0}, 1000);
    });
});