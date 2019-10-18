@extends('layouts.app')
@section('title', '成人动画')

@section('content')

@include('swiper')
@include('swiper-item')
@include('item-notice')

<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="last-video-title">
				<h2>成人动画</h2>
			</div>			
			
			@include('video-lists')

		</div>
    </div>
</div>
@endsection
