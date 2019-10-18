<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="theme-color" content="">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-status" content="{{ auth::guest() ? 'guest' :'member' }}">
	
    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>
	<meta name="description" content="最新素人無碼偷拍AV,偷拍外流,夫妻自拍,情侶自拍,最新影片,日韓有碼,日韓無碼,歐美影片,成人動畫,自拍系列">
	<meta name="keywords" content="av, 素人, 偷拍, 自拍流出, 外流, 無碼AV, 最新影片,日韓有碼,日韓無碼,歐美影片,成人動畫,自拍系列">
	@include('layouts.global-css')
	@include('layouts.global-js')
	@yield('extendCSS')
	@yield('extendJS')
</head>
<body class="drawer drawer--left">

@include('layouts.sliderbar')

<main role="main">
	@include('layouts.navbar')        
	<div class="middle-container">
	    @yield('content')
	</div>
	@include('layouts.footer')
</main>

<script type="text/javascript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-101653825-2', 'auto');
ga('send', 'pageview');



window.ChatraSetup = {
    colors: {
        buttonText: '#f0f0f0', /* chat button text color */
        buttonBg: '#565656'    /* chat button background color */
    },
	startHidden: true,
};
(function(d, w, c) {
    w.ChatraID = 'f3mAorFgcKtrvRuqf';
    var s = d.createElement('script');
    w[c] = w[c] || function() {
        (w[c].q = w[c].q || []).push(arguments);
    };
    s.async = true;
    s.src = 'https://call.chatra.io/chatra.js';
    if (d.head) d.head.appendChild(s);
})(document, window, 'Chatra');



$(function (){
@if(!session()->get('globalMessage'))
	//app.success('亲爱的用户<BR>因充值系统异常，目前已排除，如有未到帐的情况发生，请稍微等候系统查帐后将连同赔偿点数一并发送到您的帐户中，造成您的不便深感抱歉。');
@endif
});
</script>
{{ session()->put('globalMessage','1') }}
</body>
</html>
