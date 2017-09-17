$(function () {
    $(window).on("scroll", function(e) {
        if ($(this).scrollTop() > 147) {
            $('.navbar').addClass("navbar-fixed-top");
        } else {
            $('.navbar').removeClass("navbar-fixed-top");
        }

    });
});