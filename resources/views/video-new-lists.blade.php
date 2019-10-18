	<div class="last-video-title">
		<h2>最新日韩影片 <a href="{{ route('uncensored') }}">更多 <i class="fas fa-angle-double-right"></i></a></h2>
	</div>			
	<ul class="videos-list list-inline list-inline-linking">
		@foreach ($HDJapanVideos as $video)
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
						<i class="fa fa-plus" aria-hidden="true"> </i>
					</div>


					<div class="video-bottom"></div>
					@if($video['video_type'])
						<div class="av-mark av-right-bottom av-censored">有码</div>
					@else
						<div class="av-mark av-right-bottom av-uncensored">无码</div>
					@endif
					<div class="av-mark av-left-bottom av-release-date">
						{{ str_limit($video['updated_at'],10,'') }}
					</div>
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
	
	



	<div class="last-video-title">
		<h2>最新欧美影片 <a href="{{ route('united') }}">更多 <i class="fas fa-angle-double-right"></i></a></h2>
	</div>			
	<ul class="videos-list list-inline list-inline-linking">
		@foreach ($HDUnitedVideos as $video)
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
						<i class="fa fa-plus" aria-hidden="true"> </i>
					</div>

					<div class="video-bottom"></div>
					@if($video['video_type'])
						<div class="av-mark av-right-bottom av-censored">有码</div>
					@else
						<div class="av-mark av-right-bottom av-uncensored">无码</div>
					@endif
					<div class="av-mark av-left-bottom av-release-date">
						{{ str_limit($video['updated_at'],10,'') }}
					</div>
					@if($video['duration'] != '00:00:00')
						<div class="av-mark av-right-top av-duraction"> {{ $video['duration'] }}</div>
					@endif
				</a>
				<a href="{{ route('watch', [ 'avkey' => $video['avkey'] ]) }}" class="title">{{ $video['content'] ? $video['content'] : $video['title']}}</a>
				<div class="actors-line">
					@foreach ($video['actors_name'] as $id => $actor)
						<a href="{{ route('actor', [ $actor ]) }}">{{ $actor }}</a> {{ $loop->iteration != $loop->last ? ',' : '' }}
					@endforeach
				</div>
			</li>
		@endforeach
	</ul>
	

	<div class="last-video-title">
		<h2>最新动画影片 <a href="{{ route('cartoon') }}">更多 <i class="fas fa-angle-double-right"></i></a></h2>
	</div>			
	<ul class="videos-list list-inline list-inline-linking">
		@foreach ($HDCartoonVideos as $video)
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
						<i class="fa fa-plus" aria-hidden="true"> </i>
					</div>

					<div class="video-bottom"></div>
					@if($video['video_type'])
						<div class="av-mark av-right-bottom av-censored">有码</div>
					@else
						<div class="av-mark av-right-bottom av-uncensored">无码</div>
					@endif
					<div class="av-mark av-left-bottom av-release-date">
						{{ str_limit($video['updated_at'],10,'') }}
					</div>
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



	<div class="last-video-title">
		<h2>最新偷拍自拍影片 <a href="{{ route('self') }}">更多 <i class="fas fa-angle-double-right"></i></a></h2>
	</div>			
	<ul class="videos-list list-inline list-inline-linking">
		@foreach ($HDSelfVideos as $video)
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
						<i class="fa fa-plus" aria-hidden="true"> </i>
					</div>

					<div class="video-bottom"></div>
					@if($video['video_type'])
						<div class="av-mark av-right-bottom av-censored">有码</div>
					@else
						<div class="av-mark av-right-bottom av-uncensored">无码</div>
					@endif
					<div class="av-mark av-left-bottom av-release-date">
						{{ str_limit($video['updated_at'],10,'') }}
					</div>
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


