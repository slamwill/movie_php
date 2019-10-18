@extends('layouts.users')


@section('content')
<div class="row">
	<h4 class="head-title">VIP方案</h4>
	<hr>

	<div class="row vip-box">

		@foreach ($ServiceConfig as $row)
			<div class="col-xs-6 col-sm-4">
				<div class="vip-package">
					<em>VIP方案 {{$loop->iteration}}</em>

					<div class="row">
						<div class="col-xs-3 text-center"><img src="https://image.flaticon.com/icons/svg/1198/1198990.svg" width="100%"></div>
						<div class="col-xs-9">
							<div>消费 <span class="coins">{{ $row['coins'] }}</span> 点 <span class="vip-tag pull-right hidden-xs hidden-sm">{{ $vipTags[$loop->index]}}</span></div>
							<div class="vip-title">{{ $row['title'] }}</div>
							<div><button class="btn vip-btn btn-primary btn-block-mobile-only" data-id="{{$row['id']}}" data-coins="{{$row['coins']}}" data-times="{{$row['times']}}">立即升级</button></div>
						</div>
					</div>


				</div>
			</div>


		@endforeach

	</div>


</div>
<script type="text/javascript">

$('.vip-btn').unbind().click(function (){
	var data = $(this).data();
	var expired = '';
	var times = data.times/60;
	if (times == 1 || times == 24) {
		expired = times + '小时';
	}
	else {
		expired = (times / 24) + '天';		
	}
	var msg = "是否扣除 " + data.coins + " 点?<div>VIP权限将延长" + expired + "<\/div>";

	app.confirm(msg, function (){
		$.ajax({
			url: '{{ url()->current() }}',
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			//contentType:'application/json; charset=utf-8',
			cache : false,
			data: { 'id' : data.id},
			//dataType:'json',
			async: false,
			success: function (response) {
				if (response.status == 0) {
					app.error(response.message);
				}
				else if (response.status == 2) {
					app.confirm(response.message,function (){
						$('#recharge').click();
					});
				}
				else {
					app.success(response.message);
					$('#vip').click();
				}
			},
			error: function(response){
			}
		});


	});

});


</script>

@endsection
