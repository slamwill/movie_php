<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
@if(env('MINIFY_CSS'))
<link href="{{ asset('dist/all.min.css?v='.env('CSS_JS_VERSION')) }}" rel="stylesheet">
@else
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/slidebars.css') }}" rel="stylesheet">
<link href="{{ asset('css/flowplayer-skin.min.css') }}" rel="stylesheet">

{{--
<link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs-skin.css') }}" rel="stylesheet">
--}}
<link href="{{ asset('css/swiper.min.css') }}" rel="stylesheet">

<link href="{{ asset('css/drawer.min.css') }}" rel="stylesheet">

<link href="{{ asset('css/nprogress.css') }}" rel="stylesheet">

<link href="{{ asset('css/jquery.scrollbar.css') }}" rel="stylesheet">


<link href="{{ asset('css/globals.css') }}" rel="stylesheet">

@endif