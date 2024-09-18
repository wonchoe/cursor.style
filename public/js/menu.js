var tabs = $('.tabs');
var selector = $('.tabs').find('a').length;
var activeItem = tabs.find('.active');
var activeWidth = activeItem.get()[0].clientWidth - 5;
$(".selector").css({
    "display": 'block',
    "left": activeItem.position.left + "px",
    "width": activeWidth + "px"
});

$(".tabs").on("click", "a", function (e) {
    $('.tabs a').removeClass("active");
    $(this).addClass('active');
    var activeWidth = $(this).get()[0].clientWidth + 7;
    var itemPos = $(this).position();
    $(".selector").css({
        "display": 'block',
        "left": itemPos.left + "px",
        "width": activeWidth + "px"
    });
});


var url = new URL(location.href);
var c = url.searchParams.get("q");
if (c)
    if (c.length > 0) {
        menu_top.classList.remove('active');
        menu_new.classList.remove('active');
    }