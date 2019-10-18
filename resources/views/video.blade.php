@extends('layouts.app')

@section('title', $AvVideo['title'])

@section('content')

<div>
	<div class="container">

		<div class="row player-block">
			<div class="col-md-10 video-left-box">
				<div style="position: relative;">
					<div class="goto-unlock" style="display:none;">
						<a href="{{ route('user.vip')}}" class="outline">

							@guest
									<div class="text">裤子脱一半?<br>立即解锁影片!</div>
							@else
								@if( date('Y-m-d H:i:s') > Auth::user()->expired )
									<div class="text">裤子脱一半?<br>立即解锁影片!</div>
								@endif
								<!-- span class="text">敬请期待更多精彩功能!</span>
								<a><span class="radius hidden-xs"><i class="fa fa-long-arrow-right icon-right" aria-hidden="true"></i></span></a -->
							@endguest
						</a>
					</div>
						<div id="fp-hlsjs" class="fp-full fp-mute is-splash" data-aspect-ratio="12:5" style="background-color:#000;background-image:url({{  $AvVideo['cover_index'] ? \App\Classes\Common::previewToken($AvVideo['avkey'].'/preview'.( $AvVideo['cover_index'] - 1).'b.png') : config('cover_url').'/'.$AvVideo['cover'] }})"></div>

				</div>


				<div class="video-action-box">
					<div class="pull-right views-box">
						<span class="view"><i class="fas fa-eye"></i> {{ $AvVideo['views'] }}</span>
						<span class="view watch-release-date">上映时间：{{ $AvVideo['release_date'] }}</span>
					</div>



					<a href="javascript:void(0);" onclick="app.downloadConfirm('{{ $AvVideo['avkey'] }}')" class="action-button"><i class="fas fa-download fas-blue"></i> 下载<span class="hidden-xs">影片<span></a>

					<a class="action-button collection-videos {{ $boolMyFavoriteVideo ? 'favorite':''}}" data-title="{{ $AvVideo['title'] }}" data-avkey="{{ $AvVideo['avkey'] }}" id="favorite_videos" value="favorite_videos" href="#">
							<i class="fas fa-heart fas-red"></i> 收藏<span class="hidden-xs">影片</span></i>
					</a> 
					<a href="javascript:void(0);" class="action-button report-btn"><i class="fas fa-envelope fas-green"></i> <span class="hidden-xs">问题</span>回报</a>

				</div>

				<div class="video-info-box">
					<ul>
						<li style="margin-bottom: 5px;">
							@if($AvVideo['video_type'])
								<span class="av-mark-title av-censored">有码</span>
							@else
								<span class="av-mark-title av-uncensored">无码</span>
							@endif
							<span class="title">
								@if($AvVideo['content'])
									{{ $AvVideo['content'] }}
								@else
									{{ $AvVideo['title'] }}
								@endif							
							</span>
							<span class="avkey">{{ $AvVideo['avkey'] }}</span>
						</li>

						@if($AvVideo['actors_name'])
						<li>
							<label>演出女优：</label>
							<span>
								@foreach ($AvVideo['actors_name'] as $id => $actor)
									<span class="badge badge-danger add-actors">
										<a href="{{ route('actor', [ $actor ]) }}">{{$actor}}</a>
										<a href="javascript:;" class="collection-actors hide" data-actor="{{$actor}}"><i class="fa fa-plus-square" aria-hidden="true" data-toggle="tooltip" data-original-title="加到我的女優"></i></a>
									</span> 
								@endforeach
							</span>
						</li>
						@endif
						<li>
							<label>影片标签：</label>
							<span>
								@foreach ($AvVideo['tags_name'] as $id => $tag)
									<a href="{{ route('tag',[ $tag ]) }}" class="badge badge-secondary">{{$tag}}</a>
									{{--  <a href="{{ route('tag',[ \Crypt::encrypt($id.'|'.$tag) ]) }}" class="badge badge-secondary">{{$tag}}</a>  --}}
								@endforeach
							</span>
						</li>
					</ul>
				</div>
			</div>
			
			<div class="col-md-2 video-right-box hidden-xs">
				<div class="list-group">
					<div class="list-group-item video-title">热门相关影片</div>
						<div class="list-group-item scrollbar-dynamic">
						@foreach ($RightBoxVideos as $video)
							<div class="mtop-dark clearfix">
								<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}">
									<div class="">
										<img src="{{ $video['cover_index'] ? \App\Classes\Common::previewToken($video['avkey'].'/preview'.( $video['cover_index'] - 1).'s.png') : config('cover_url').'/'.$video['cover'] }}" width="100%" height="100%" title="{{ $video['title'] }}">
									</div>
								</a>
								<div class="sm-right-box">
									<div class="sm-title"><a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}">{{ $video['content'] ? $video['content'] : $video['title']}}</a></div>
									<div class="sm-view"><i class="fas fa-play-circle"></i> {{ $video['views'] }}</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@if(!$AvVideo['is_free'])
<div class="container">
    <div class="row">
		<div class="last-video-title preview">
			<h2>预览图片</h2>
		</div>	
		<ul class="videos-list list-inline list-inline-linking preview">
			@foreach (range(0,9) as $i)
				<li><img src="{!! App\Classes\Common::previewToken($AvVideo['avkey'].'/preview'.($i*2+1).'s.png') !!}" width="100%"></li>
			@endforeach
		</ul>
	</div>
</div>
@endif

<div class="container">
    <div class="row">
		<div class="last-video-title preview">
			<h2>猜你喜欢</h2>
		</div>
		<ul class="videos-list list-inline list-inline-linking">
			{{-- {{ dd($MaybeYouLike) }} --}}
			@foreach ($MaybeYouLike as $video)
				<li>
					<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}" class="a-cover" data-preview="{!! App\Classes\Common::previewToken($video['avkey'].'/preview.mp4') !!}">
						<img src="{{ $video['cover_index'] ? \App\Classes\Common::previewToken($video['avkey'].'/preview'.( $video['cover_index'] - 1).'s.png') : config('cover_url').'/'.$video['cover'] }}" width="100%">
						<div class="progress" style="height:3px;background-color: transparent;">
						  <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="preview-video"></div>

						@if($video['is_free'])
							<span class="video-free">免费</span>
						@elseif(str_limit($video['updated_at'],10,'') == date('Y-m-d'))
							<span class="video-tag">最新</span>
						@endif

						<div class="av-mark av-right-top av-forever collection-videos-small" data-title="{{ $video['title'] }}" data-avkey="{{ $video['avkey'] }}"  id="favorite_videos-small" value="favorite_videos-small" data-action="add" data-placement="left" data-toggle="tooltip" title="收藏影片">
							<i class="fa fa-plus" aria-hidden="true"></i>
						</div>
						<div class="video-bottom"></div>

						@if($video['video_type'])
							<div class="av-mark av-right-bottom av-censored">有码</div>
						@else
							<div class="av-mark av-right-bottom av-uncensored">无码</div>
						@endif
						<div class="av-mark av-left-bottom av-release-date"> {{ str_limit($video['updated_at'],10,'') }}</div>
						@if($video['duration'] != '00:00:00')
							<div class="av-mark av-right-top av-duraction"> {{ $video['duration'] }}</div>
						@endif

					</a>
					<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}" class="title">{{ $video['content'] ? $video['content'] : $video['title']}}</a>
					<div class="actors-line">
						@foreach ($video['actors_name'] as $id => $actor)
							<a href="{{ route('actor', [ $actor ]) }}">{{ $actor }}</a> {{ $loop->iteration != $loop->last ? ',' : '' }}
							{{--  <a href="{{ route('actor', [ \Crypt::encrypt($id.'|'.$actor) ]) }}">{{ $actor }}</a> {{ $loop->iteration != $loop->last ? ',' : '' }}  --}}
						@endforeach
					</div>
				</li>
			@endforeach
		</ul>
    </div>
</div>

<script type="text/javascript">
var avkey = '{{ $AvVideo['avkey'] }}';
app.api.watchs(avkey);
$('.scrollbar-dynamic').scrollbar();
$('.download-confirm').click(function (event){
	var href = $(this).attr('href');
	if (!href) {
		app.downloadConfirm(avkey);
		event.preventDefault();
		return false;
	}
});
$('.report-btn').on('click', function(){Chatra('show');Chatra('openChat', true);});



</script>
	<script type="text/javascript">
	{{--
	$(function (){
		var player = app.player();
		player.firstLoader = true;
		player.ready(function() {

			@guest

			@if(!$AvVideo['is_free'])
				@if( ( !Auth::check() || !auth::user()->expired || auth::user()->expired < date('Y-m-d H:i:s') ) )
					this.duration = function() {
					return 60;
				}
				@endif
			@endif


			@endguest
			this.on('pause',function (){
				$('.goto-unlock').show();
			});	

			this.on('play',function (){
				if (player.firstLoader && app.isMobile()) {
					player.firstLoader = false;
					window._playerWatchInterval = setInterval(function (){
						if (!isNaN(player.duration())) {
							clearInterval(window._playerWatchInterval);
							player.currentTime(0.1);
							player.play();

						}
						player.playCount++;
					}, 1000);

				}
				$('.goto-unlock').hide();
			});
			this.on('volumechange',function (){
				$.cookie('PlayerVolume', this.muted() ? 0 : this.volume(), { expires: 365 });
			});
			this.volume($.cookie('PlayerVolume') ? $.cookie('PlayerVolume') : 0.5);

		});


	
	});
	--}}

		flowplayer(function (api, root) {
			var fsbutton = root.querySelector(".fp-fullscreen");

			var common = flowplayer.common,
			    bean = flowplayer.bean,
			    bw = common.createElement("strong", {"class": "fas fa-backward fa-lg"}, ""),
			    fw = common.createElement("strong", {"class": "fas fa-forward fa-lg"}, "");
			
			bean.on(bw, "click", function () {
			  var target = api.video.time - 10;
			  if (target >= 0 && !api.seeking) {
			    api.seek(target);
			  }
			});;
			
			bean.on(fw, "click", function () {
			  var video = api.video,
			      target = video.time + 10;
			  if (target <= video.duration && !api.seeking) {
			    api.seek(target);
			  }
			});;
			$('.fp-controls .fp-playbtn').before(bw);
			$('.fp-controls .fp-playbtn').after(fw);

			// append fullscreen button after HD menu is added on ready
			api.on("ready", function () {
				root.querySelector(".fp-controls").appendChild(fsbutton);
			});

			@if(empty(Auth::user()->expired) || date('Y-m-d H:i:s') > Auth::user()->expired )
			api.on("pause", function(e, api) {
				$('.goto-unlock').show();
			});
			api.on("resume", function(e, api) {
				$('.goto-unlock').hide();
			});
			@endif
		});
		 
		flowplayer("#fp-hlsjs", {
			splash: false,
			ratio: 9/16,
			clip: {
				sources: [{ type: "application/x-mpegurl",src: "{{ route('api.play', [ $AvVideo['avkey'] ]) }}?token={{ Crypt::encryptString(\Session::getId()) }}"}]   
			},
			embed: false
		});


	</script>

@endsection