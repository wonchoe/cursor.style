<!DOCTYPE html>
<html lang="ru">
<head>
    <title>@yield('title')</title> 
    <meta name="description" content="@yield('descr')"/>        
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>    
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta property="og:title" content="Бесплатные наборы крутых курсоров - прокачай свой!" />
    <meta property="og:image" content="https://sun9-72.userapi.com/c850616/v850616098/1c5b71/5jaFd8X4TR0.jpg" />        
    <link rel="stylesheet" href="{{ secure_asset('/css/modal.css')}}"/>    
    @yield('lib_top')                 
    <script src="{{ secure_asset('/js/init.js')}}"></script>
</head>

@include('layouts.loader')

<body>                
    

    @yield('main')

    @yield('lib_bottom')  
    
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(55288747, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/55288747" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>