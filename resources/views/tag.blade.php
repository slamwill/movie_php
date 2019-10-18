@extends('layouts.app')

@section('content')



<div class="container">
    <div class="row ">
        <div class="col-md-12">

			<div class="left-video-title">
				<h3>标签「{{ $tag }}」</h3>
			</div>			
			
			@include('video-lists')

		</div>
    </div>
</div>
@endsection
