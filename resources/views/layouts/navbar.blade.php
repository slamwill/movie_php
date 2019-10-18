
<div class="header">
   <div class="container">
	  <div class="header-container">
         <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
               <div class="nav-toggle rwd-slidebar drawer-toggle"></div>
               <a class="nav-brand mobile-logo" href="/">
                  <img src="{{ asset('/images/logo.png') }}">
                </a>
            </div>

							


            @guest
            <div class="nav-header pull-right header-home-icon hidden-md hidden-sm hidden-lg">
               <a class="nav-brand" href="{{ route('login') }}"><i class="fas fa-user"></i><span>登录</span></a>
            </div>
			@else
            <div class="nav-header pull-right header-home-icon hidden-md hidden-sm hidden-lg">
               <a class="nav-brand" href="{{ route('user.personal') }}"><img src="/images/user/avatar.png" class="personal-avatar" width="32"></a>
            </div>

            @endguest
            <div class="nav-header pull-right header-search-icon hide">
               <a class="nav-brand" href="javascript:;"><i class="fa fa-search" aria-hidden="true"></i></a>
            </div>

	
			<div class="nav-header nav-menus-wrapper hidden-xs" style="transition-property: none;">
               <div id="main-menu">
                  <ul class="nav-menu slide-menu">
                     <li class="{{ Request::is('latest') ? 'active' : '' }}"><a href="{{ route('latest') }}">最新影片</a></li>
					 <li class="{{ Request::is('censored') ? 'active' : '' }}"><a href="{{ route('censored') }}">有码</a></li>
					 <li class="{{ Request::is('uncensored') ? 'active' : '' }}"><a href="{{ route('uncensored') }}">无码</a></li>
					 <li class="{{ Request::is('united') ? 'active' : '' }}"><a href="{{ route('united') }}">欧美</a></li>
					 <li class="{{ Request::is('cartoon') ? 'active' : '' }}"><a href="{{ route('cartoon') }}">动画</a></li>
					 <li class="{{ Request::is('self') ? 'active' : '' }}"><a href="{{ route('self') }}">短片</a></li>
					 <li class="{{ Request::is('free') ? 'active' : '' }}"><a href="{{ route('free') }}">免费</a></li>
                  </ul>
               </div>
               <ul class="nav-menu mobile-submenu">
               </ul>


               <ul class="nav-menu nav-menu-social align-to-right">
				  <li class="line-height-70">&nbsp;</li>
                  <li class="nav-search-bar">
                     <div class="search">
                        <input type="text" class="searchTerm" id="search-text-web" placeholder="关键字搜索" maxlength="10">
                        <button type="submit" class="searchButton" id="search-web-submit">
                        <i class="fa fa-search"></i>
                        </button>
                     </div>
                  </li>
				  @guest
                  <li class="fs-login">
					<a href="{{ route('register') }}"><img src="/images/reg.png" class="nav-top-reg-icon"><i class="fa fa-registered hide" aria-hidden="true"></i> 注册</a> &nbsp; <a href="{{ route('login') }}"><img src="/images/login.png" class="nav-top-login-icon"><i class="fa fa-sign-in hide" aria-hidden="true"></i> 登录</a>
				  </li>
                  @else
                  <li class="dropdown">
                     <a href="{{ route('user.personal') }}" class="dropdown-toggle logined" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
						<img src="/images/user/avatar.png" class="personal-avatar" width="40" height="40">
                     </a>


                     <ul class="dropdown-menu personal-box">
                        <li>
							<div class="user-wrap-arrow"></div>
							<div class="user-top">
								<a href="{{ route('user.personal') }}"><img src="/images/user/avatar.png" class="personal-avatar"  width="56" height="56"><span class="name">{{Auth::User()->name}}</span></a>
							</div>

							<div class="user-card">
								<div class="card-box">
									<div class="card-state">
										<div class="vip">
										@if (Auth::User()->expired > date('Y-m-d H:i:s'))
											黄金VIP会员 
										@else
											免费会员 <a href="{{ route('user.vip') }}" class="level-up"  data-toggle="tooltip" title="" data-original-title="升级VIP全站无限看">升级VIP</a>
										@endif 
										</div> 
									</div>
									<div class="card-tip">{{Auth::User()->expired ? Auth::User()->expired : Auth::User()->created_at}} 到期</div>
								</div>
							</div>

							<div class="row user-link">
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
							<div class="user-exit">
								<a href="#"  onclick="app.logout();">
									<i class="fas fa-sign-out-alt"></i> 退出账号
								</a>
							</div>
                           <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                           </form>
                        </li>
                     </ul>
                  </li>
                  @endguest						
               </ul>
            </div>
         </nav>
      </div>
   </div>
   <div class="row header-search-bar" style="display:none;">
      <div class="search">
         <i class="fa fa-search"></i>
         <input type="text" class="" placeholder="请输入关键字" id="search-text-mobile" maxlength="10">
         <i class="fa fa-times-circle" aria-hidden="true"></i>
      </div>
      <a href="#" class="cancel-header-search-bar">取消</a>
      <a href="#" class="submit-header-search-bar">送出</a>
   </div>




	<nav class="sub-header">
		<div class="container">

		<div class="header-tags-swiper-item-container ">

			<div class="swiper-wrapper">

				<div class="swiper-slide"><a href="{{ route('latest') }}" class="{{ Request::is('latest') ? 'active' : '' }}">最新影片</a></div>
				<div class="swiper-slide"><a href="{{ route('censored') }}" class="{{ Request::is('censored') ? 'active' : '' }}">有码</a></div>
				<div class="swiper-slide"><a href="{{ route('uncensored') }}" class="{{ Request::is('uncensored') ? 'active' : '' }}">无码</a></div>
				<div class="swiper-slide"><a href="{{ route('united') }}" class="{{ Request::is('united') ? 'active' : '' }}">欧美</a></div>
				<div class="swiper-slide"><a href="{{ route('cartoon') }}" class="{{ Request::is('cartoon') ? 'active' : '' }}">动画</a></div>
				<div class="swiper-slide"><a href="{{ route('self') }}" class="{{ Request::is('self') ? 'active' : '' }}">短片</a></div>
				<div class="swiper-slide"><a href="{{ route('free') }}" class="{{ Request::is('free') ? 'active' : '' }}">免费</a></div>

			</div>
		</div>


		
		
		
		</div>
	</nav>





</div>
<script type="text/javascript">

var headerTagSwiper = new Swiper('.header-tags-swiper-item-container', {
	slidesPerView: 'auto',
	centeredSlides: false,
	spaceBetween: 0,
	loop: false,
	freeMode: false,
		/*
	autoplay: {
		delay: 3500,
	},*/
});
/*
$('.header-tags-swiper-item-container').hover(function() {
	headerTagSwiper.autoplay.stop();
}, function() {
	headerTagSwiper.autoplay.start();
}).on('touchend',function() {
	setTimeout(function (){
		headerTagSwiper.autoplay.start();
	},1000);
});*/
var i,w,t,j;
i = $('.header-tags-swiper-item-container a').index($('.header-tags-swiper-item-container a.active'));
if (i > -1 && i > 3){
	w = $('.header-tags-swiper-item-container').width();
	t = 0,j=0;
	$.each($('.header-tags-swiper-item-container div.swiper-slide'),function (){
		t = t + $(this).width();
		//console.log(t,w,j,i);
		if (t > w) {
			headerTagSwiper.slideTo(i - 1, 0);
			return false;
		}
		if (j >= i)	{
			return false;
		}

		j++;
	
	});
}
</script>

