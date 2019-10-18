@extends('layouts.app')
@section('title', '最新影片')

@section('content')


<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="last-video-title">
				<h2>最新影片</h2>
			</div>			
			
			@include('video-lists')

		</div>
    </div>
</div>
@endsection
