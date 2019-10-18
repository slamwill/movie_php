@extends('layouts.app')
@section('title', '欧美影片')
@section('content')

@include('swiper')
@include('swiper-item')
@include('item-notice')

<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="last-video-title">
				<h2>欧美影片</h2>
			</div>			
			
			@include('video-lists')

		</div>
    </div>
</div>
@endsection
