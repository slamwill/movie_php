<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-status" content="{{ auth::guest() ? 'guest' :'member' }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('pageTitle','會員中心')</title>

	@include('layouts.global-css')
	<link href="{{ asset('css/user-style.css') }}" rel="stylesheet">


	@include('layouts.global-js')

	@yield('extendCSS')
	@yield('extendJS')
</head>
<style>


</style>
<body class="drawer drawer--left">

@include('layouts.sliderbar')
<main role="main">

	@include('layouts.navbar')
	<div class="container">



		<div class="row user-box middle-container">
			<div class="col-md-3 col-lg-2 left-box">


				<div class="row user-link mobile">
					<div class="col-xs-4">
						<a href="{{ route('user.personal') }}"><span class="icon"><span class="fas fa-cog fa-lg"></span></span><div class="text">个人设置</div></a>
					</div>
					<div class="col-xs-4">
						<a href="{{ route('user.recharge') }}"><span class="icon"><span class="fab fa-cc-amazon-pay fa-lg"></span></span><div class="text">会员充值</div></a>
					</div>
					<div class="col-xs-4">
						<a href="{{ route('user.vip') }}"><span class="icon"><span class="fas fa-crown fa-lg"></span></span><div class="text">VIP方案</div></a>
					</div>
					<div class="col-xs-4">
						<a href="{{ route('user.watches') }}"><span class="icon"><span class="fas fa-history fa-lg"></span></span><div class="text">播放记录</div></a>
					</div>
					<div class="col-xs-4">
						<a href="{{ route('user.videos') }}"><span class="icon"><span class="fas fa-align-left fa-lg"></span></span><div class="text">我的收藏</div></a>
					</div>
					<div class="col-xs-4">
						<a href="{{ route('user.transfer') }}"><span class="icon"><span class="fas fa-file-alt fa-lg"></span></span><div class="text">交易记录</div></a>
					</div>

				</div>
				<div class="list-group" id="user-left-menu-item">
				   <div class="card">
						<div class="personal-info">
							<img src="/images/user/avatar.png" class="personal-avatar">
							<div class="personal-detail">
								<span class="personal-name">{{ Auth::user()->name }}</span>
							</div>
							<div class="personal-detail" style="font-size:12px;color:#c1c1c1">
								目前点数：<span class="personal-name">{{ Auth::user()->coins }}</span> 点
							</div>

							@if(Auth::user()->expired && Auth::User()->expired > date('Y-m-d H:i:s'))
								<span class="expired-tag" data-toggle="tooltip" title="VIP到期时间：{{ Auth::user()->expired }}">{{ Auth::user()->expired }}到期</span>
							@else
								<span class="level-up" onclick="$('#vip').click();" data-toggle="tooltip" title="升级VIP全站无限看">升级VIP会员</span>
							@endif

						</div>
						<div class="card-body">
						
							<a href="{{ route('user.personal') }}" class="list-group-item {{ Request::path() == 'user/personal' ? 'active' : '' }}" id="personal">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fas fa-cog fa-lg"></span></div>
									<div class="col-md-auto">个人设置</div>
								</div>
							</a>

							<a href="{{ route('user.recharge') }}" class="list-group-item {{ Request::path() == 'user/recharge' ? 'active' : '' }}" id="recharge">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fab fa-cc-amazon-pay fa-lg"></span></div>
									<div class="col-md-auto">会员充值</div>
								</div>
							</a>
							<a href="{{ route('user.vip') }}" class="list-group-item {{ Request::path() == 'user/vip' ? 'active' : '' }}" id="vip">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fas fa-crown fa-lg"></span></div>
									<div class="col-md-auto">VIP方案</div>
								</div>
							</a>

							<a href="{{ route('user.watches') }}" class="list-group-item {{ Request::path() == 'user/watches' ? 'active' : '' }}">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fas fa-history fa-lg"></span></div>
									<div class="col-md-auto">播放记录</div>
								</div>
							</a>

							<a href="{{ route('user.videos') }}" class="list-group-item {{ Request::path() == 'user/videos' ? 'active' : '' }}">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fas fa-align-left fa-lg"></span></div>
									<div class="col-md-auto">我的收藏</div>
								</div>
							</a>
							<a href="{{ route('user.transfer') }}" class="list-group-item {{ Request::path() == 'user/transfer' ? 'active' : '' }}">
								<div class="row justify-content-md-center">
									<div class="col col-md-5 text-center"><span class="fas fa-file-alt fa-lg"></span></div>
									<div class="col-md-auto">交易记录</div>
								</div>
							</a>
						 </div>

				     </div>

				</div>

			</div>
			
			<div class="col-md-9 col-lg-10 right-box">
				<div class="panel panel-default">

					<div class="panel-body">

						@yield('content')

					</div>
				</div>
				@include('layouts.global-alert')
			</div>
		</div>
	</div>
	@include('layouts.footer')
</main>


<script type="text/javascript">
$(function (){
	


	$(document).pjax('#user-left-menu-item a.list-group-item,.user-link.mobile a,.user-pjax,ul.pagination a', '.user-box');


	$(document).off('submit').on('submit', 'form[data-pjax]', function(event) {
		var target = $(this).data('pjax');
		$.pjax.submit(event, target);
	})
	$(document).on('click','#user-left-menu-item a.list-group-item', function (){
		$('#user-left-menu-item a.list-group-item').removeClass('active');
		$(this).addClass('active');
	});
	$(document).on('pjax:success', function(event) {

		$('[data-toggle="tooltip"]').tooltip();    
	});


});
</script>
	
</body>
</html>
