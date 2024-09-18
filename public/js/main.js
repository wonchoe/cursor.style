$('.how__btn').on('click', function () {
    var then = $(this);
    var index = then.index();
    $('.how__btn').removeClass('active');
    then.addClass('active');
    $('.how__tab.active').fadeOut(300, function () {
        $('.how__tab.active').removeClass('active');
        $('.how__tab:eq(' + index + ')').addClass('active').fadeIn(300);
    });
});


$('.burger').on('click', function () {
    $('.mobile__nav').fadeIn(300).addClass('active');
});

$('.close').on('click', function () {
    $('.mobile__nav').removeClass('active').fadeOut(300);
});

$('.banner__tab_1 .banner__text').mouseenter(function () {
    $('.banner__tab_1').hide();
    $('.banner__tab_2').css({
        'display': 'flex'
    });
})
$('.banner__tab_2 .banner__text').mouseleave(function () {
    $('.banner__tab_2').hide();
    $('.banner__tab_1').css({
        'display': 'flex'
    });
})

if ($('.js-tilt').length != 0)
    $('.js-tilt').tilt({
        scale: 1.1
    })
