<style>

</style>

<header role="banner">
    <nav class="drawer-nav" role="navigation">
		<div class="user-info">
			@guest
			<div class="col-xs-12 mb-10">



				<img src="/images/user/avatar.png" class="personal-avatar" width="50">
				<div class="m-user-side">
					<a href="{{ route('login') }}">登录</a>
					<span class="v-bar"></span>
					<a href="{{ route('register') }}">注册</a>
				</div>


			</div>
			@else
			<div class="col-xs-12 mb-10">

				<img src="/images/user/avatar.png" class="personal-avatar" width="50">
				<div class="m-user-side">
					<a href="{{ route('user.personal') }}">{{ Auth::user()->name }}</a>
				</div>





			</div>
			@endguest



		</div>

		@auth
							<div class="user-card">
								<div class="card-box">
									<div class="card-state">
										<div class="vip">
										@if (Auth::User()->expired > date('Y-m-d H:i:s'))
											黄金VIP会员 
										@else
											免费会员 <a href="{{ route('user.vip') }}" class="level-up">升级VIP</a>
										@endif 
										</div> 
									</div>
									<div class="card-tip">{{Auth::User()->expired ? Auth::User()->expired : Auth::User()->created_at}} 到期</div>
								</div>
							</div>
		@endauth
			<div class="m-side-list">

				<div>
					<span class="icon"><span class="fas fa-cog fa-lg"></span></span>
					<a href="{{ route('user.personal') }}">个人设置</a>
				</div>
				<div>
					<span class="icon"><span class="fab fa-cc-amazon-pay fa-lg"></span></span>
					<a href="{{ route('user.recharge') }}">会员充值</a>
				</div>
				<div>
					<span class="icon"><span class="fas fa-crown fa-lg"></span></span>
					<a href="{{ route('user.vip') }}">VIP方案</a>
				</div>
				<div>
					<span class="icon"><span class="fas fa-history fa-lg"></span></span>
					<a href="{{ route('user.watches') }}">播放记录</a>
				</div>
				<div>
					<span class="icon"><span class="fas fa-align-left fa-lg"></span></span>
					<a href="{{ route('user.videos') }}">我的收藏</a>
				</div>
				<div>
					<span class="icon"><span class="fas fa-file-alt fa-lg"></span></span>
					<a href="{{ route('user.transfer') }}">交易记录</a>
				</div>
			   
		@auth
				<div>
					<span class="icon"><span class="fas fa-sign-out-alt fa-lg"></span></span>
					<a  href="#" onclick="app.logout();">退出账号</a>
				</div>
		@endauth
			</div>


    </nav>
</header>



<script type="text/javascript">
$(function (){
	$('.drawer').drawer({
		class: {
			nav: 'drawer-nav',
			toggle: 'drawer-toggle',
			overlay: 'drawer-overlay',
			open: 'drawer-open',
			close: 'drawer-close',
			dropdown: 'drawer-dropdown'
		},
		iscroll: {
		  mouseWheel: false,
		  preventDefault: false
		},
		showOverlay: true
	});

	//$('.drawer-overlay').on('touchstart',function() {
	//	$('.drawer').drawer('close');
	//});
	$('.drawer-nav,.drawer-overlay').on('touchmove',function(e) {
		e.preventDefault();
		e.stopPropagation();
	});

});
</script>
