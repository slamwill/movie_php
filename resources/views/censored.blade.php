@extends('layouts.app')
@section('title', '手機免費成人線上影音｜有碼影片區')

@section('content')

@include('swiper')
@include('swiper-item')

<div class="container">
    <div class="row">
        <div class="col-md-12">
			<div class="last-video-title">
				<h2 class="f-blue">Japan and Korea /<b>日韩有码</b></h2>
			</div>			
			@include('video-lists')
		</div>
    </div>
</div>
@endsection
