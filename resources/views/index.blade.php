@extends('layouts.app')
@section('title', '最新影片')
@section('content')

<div class="index-swiper-container">
	<div class="swiper-wrapper">

		{{-- Banner --}}
		@if($IndexBannerArray and $agent->isMobile())
			@foreach ($IndexBannerArray as $key => $item)
			<div class="swiper-slide banner">
				<a href="{{ route('banner',[ \Crypt::encrypt($item['id'].'|'.$item['url']) ])}}"><img src="{{asset('uploads/' . $item['image_2'])}}" height="480"></a>
			</div>
			@endforeach
		@else
			@foreach ($IndexBannerArray as $key => $item)
				<div class="swiper-slide banner">
					<a href="{{ route('banner',[ \Crypt::encrypt($item['id'].'|'.$item['url']) ])}}"><img src="{{asset('uploads/' . $item['image_1'])}}" height="480"></a>
				</div>
			@endforeach
		@endif

		@if($IndexVideoArray)
			@foreach ($IndexVideoArray as $key_IndexVideoArray => $IndexVideoTypes)
				<div class="swiper-slide">
					<div class="sectionBanner">
					@foreach ($IndexVideoTypes as $key_IndexVideoTypes => $IndexVideoType)
						@if($key_IndexVideoTypes == 0)
							<div class="col-md-6 left-banner" style="">
								<a href="{{ route('watch', [ 'avkey' => $IndexVideoType['avkey'] ]) }}">
									<div class="image" style="background-image:url({{ $IndexVideoType['cover_index'] ? \App\Classes\Common::previewToken( $IndexVideoType['avkey'].'/preview'.(  $IndexVideoType['cover_index'] - 1).'b.png') : config('cover_url').'/'.$IndexVideoType['cover'] }});"></div>        
									<div class="viewH4">
										<h4>{{ $IndexVideoType['content'] ? $IndexVideoType['content'] : $IndexVideoType['title']}}</h4>
									</div>
								</a>
							</div>
							<div class="col-md-6 right-banner" style="">
						@else
								<a href="{{ route('watch', [ 'avkey' => $IndexVideoType['avkey'] ]) }}" tabindex="-1">
									<div class="col-w50">
										<div class="image-box" style="background-image:url({{ $IndexVideoType['cover_index'] ? \App\Classes\Common::previewToken( $IndexVideoType['avkey'].'/preview'.(  $IndexVideoType['cover_index'] - 1).'b.png') : config('cover_url').'/'.$IndexVideoType['cover'] }});"></div>
										<div class="viewH4">
											<h4>{{ $IndexVideoType['content'] ? $IndexVideoType['content'] : $IndexVideoType['title']}}</h4>
										</div>
									</div>
								</a>
						@endif
					@endforeach
							</div>
					</div>
				</div>
			@endforeach
		@endif

	</div>
</div>


<script type="text/javascript">
$(function (){

	var swiper = new Swiper('.index-swiper-container', {
		autoHeight: true,
		pagination: {
			el: '.swiper-pagination',
			//type: 'fraction',
			dynamicBullets: true,
		},
		loop: true,
		navigation: {
			// nextEl: '.swiper-button-next',
			// prevEl: '.swiper-button-prev',
		},
		autoplay: {
			delay: 5000,
		},
	});
});
</script>

<div class="nav-header-top hidden-xs banner-bottom-string">
   <div class="container text-right">
	請使用 <span class="orange">Ctrl+D</span> 進行收藏本站!  |  获取地址邮箱：<span class="orange">avtiger.001@gmail.com</span>
	</div>
</div>


<div class="container">
    <div class="row ">
        <div class="col-md-12">

			@include('video-new-lists')
            
		</div>

    </div>
</div>

@endsection
