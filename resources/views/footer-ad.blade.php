<div class="footer-ad">
	<div class="footer-swiper-item-container ">
		<!-- Add Arrows -->
		<div class="swiper-wrapper">
			@guest
				<div class="swiper-slide"><a href="{{ $agent->isMobile() ? route('register') : 'javascript:av.registerPopup();' }}" class="blinking">免费注册会员，最新影片爽爽看</a></div>
			@else
				<div class="swiper-slide"><a class="blinking">免费注册会员，最新影片爽爽看</a></div>
			@endguest
			<div class="swiper-slide"><a href="#">记住地址邮箱：avtiger.001@gmail.com</a></div>
		</div>
	</div>
</div>

<script type="text/javascript">
var footerADSwiper = new Swiper('.footer-swiper-item-container', {
	slidesPerView: 'auto',
	centeredSlides: false,
	spaceBetween: 0,
	loop: true,
	freeMode: false,
	autoplay: {
		delay: 3000,
	},
});

$('.footer-swiper-item-container').hover(function() {
	footerADSwiper.autoplay.stop();
}, function() {
	footerADSwiper.autoplay.start();
}).on('touchend',function() {
	setTimeout(function (){
		footerADSwiper.autoplay.start();
	},1000);
});
</script>