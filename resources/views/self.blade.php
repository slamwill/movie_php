@extends('layouts.app')
@section('title', '国产精品')
@section('content')


<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="last-video-title">
				<h2>国产精品</h2>
			</div>			
			
			@include('video-lists')

		</div>
    </div>
</div>
@endsection
