@extends('layouts.app')
@section('title', '关键词搜询「{{ $keyword }}」')

@section('content')


<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="last-video-title">
				<h2>关键词搜索「{{ $keyword }}」</h2>
			</div>			

			@include('video-lists')

		</div>
    </div>
</div>



<script type="text/javascript">
$(function (){
	$.each($('ul.videos-list a.title'), function (){
		app.highlightWords('{{ $keyword }}', this);
	});

	$.each($('div.actors-line a'), function (){
		app.highlightWords('{{ $keyword }}', this);
	});
});
</script>

@endsection
