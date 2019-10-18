@extends('layouts.users')

@section('content')
<div class="row">
	<h4 class="head-title">我的收藏</h4>
@if (!$AvVideos)
	<div class="no-record">
		<img src="{{ url('images/user/emptyIcons.png') }}">
		<div>这里暂时没有任何收藏影片</div>
	</div>
@else


	<ul class="videos-list list-inline list-inline-linking">
		@foreach ($AvVideos as $video)
			<li>
				<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}" class="a-cover" data-preview="{!! App\Classes\Common::previewToken($video['avkey'].'/preview.mp4') !!}">
					<img src="{{ $video['cover_index'] ? \App\Classes\Common::previewToken($video['avkey'].'/preview'.( $video['cover_index'] - 1).'s.png') : config('cover_url').'/'.$video['cover'] }}" width="100%">
					<div class="progress" style="height:3px;background-color: transparent;">
					  <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div class="preview-video"></div>					

					<div class="av-mark av-right-top av-forever collection-videos-small" data-title="{{ $video['title'] }}" data-avkey="{{ $video['avkey'] }}"  id="favorite_videos-small" value="favorite_videos-small" data-action="delete" data-placement="left" data-toggle="tooltip" title="移除影片">
						<i class="fas fa-trash-alt"></i>
					</div>

					@if($video['is_free'])
						<span class="video-free">免费</span>
					@elseif(str_limit($video['updated_at'],10,'') == date('Y-m-d'))
						<span class="video-tag">最新</span>
					@endif

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
				<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}" class="title">{{ $video['title'] }}</a>
				<div class="actors-line">
					@foreach ($video['actors_name'] as $id => $actor)
						<a href="{{ route('actor', [ $actor ]) }}">{{ $actor }}</a> {{ $loop->iteration != $loop->last ? ',' : '' }}
					@endforeach
				</div>
			</li>
		@endforeach


	</ul>
	<center>
		{{ $links }}
	</center>
@endif

</div>

@endsection
