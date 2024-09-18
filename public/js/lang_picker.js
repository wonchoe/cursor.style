function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/; domain=cursor.style; secure; SameSite=none";
}

//$(document).ready(function () {
//    $(".lang-flag").click(function () {
//        $(".language-dropdown").toggleClass("open");
//    });
//    $("ul.lang-list li").click(function () {
//        $("ul.lang-list li").removeClass("selected");
//        $(this).addClass("selected");
//        if ($(this).hasClass('lang-en')) {
//            $(".language-dropdown").find(".lang-flag").addClass("lang-en").removeClass("lang-ru").removeClass("lang-es");
//            setCookie('lang','en',356);
//            location.href = 'https://en.cursor.style'+window.location.pathname;
//        } else if ($(this).hasClass('lang-ru')) {
//            $(".language-dropdown").find(".lang-flag").addClass("lang-ru").removeClass("lang-en").removeClass("lang-es");
//            setCookie('lang','ru',356);            
//            location.href = 'https://cursor.style'+window.location.pathname;
//        } else if ($(this).hasClass('lang-es')) {
//            $(".language-dropdown").find(".lang-flag").addClass("lang-es").removeClass("lang-ru").removeClass("lang-en");
//            setCookie('lang','es',356);            
//            location.href = 'https://es.cursor.style'+window.location.pathname;
//        }
//        $(".language-dropdown").removeClass("open");
//    });
//    setTimeout(function(){
//    $(".lang-flag").addClass('lang-'+document.documentElement.lang);
//    $(".lang.lang-"+document.documentElement.lang).addClass('selected');
//    },50);
//    
//})
