@extends('layouts.app')
@section('content')
<style>
footer {
	display: none;
}

</style>
<div class="girl-bg"></div>
<div class="login-box ">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6 col-md-offset-3 col-lg-offset-4">

				<div class="panel panel-default">

					<div class="panel-body">
						<div class="title">会 员 登 录</div>
						
						<form class="form-horizontal" method="POST" action="{{ route('login') }}" id="MasterForm">
							{{ csrf_field() }}

							<div class="form-group">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="name" class=" col-md-3 col-xs-3 control-label form-control-static">帐号</label>

									<div class="col-md-9 col-xs-9 padding-0">
										<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="6-12英文字母及数字组合">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="password" class="col-md-3 col-xs-3 control-label form-control-static">密码</label>

									<div class="col-md-9 col-xs-9 padding-0">
										<input id="password" type="password" class="form-control" name="password" required placeholder="请输入您的密码">
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="input-text col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-1 col-xs-6 col-xs-offset-1">
									<label for="mobile-captcha" class="col-md-5 col-xs-5 control-label form-control-static">验证码</label>                 
									<div class="col-md-7 col-xs-7 padding-0">
										<input id="mobile-captcha" class="form-control" type="text" name="captcha" required placeholder="验证码"  maxlength="4">

									</div>
								</div>
								<span class="col-md-4 col-xs-5 col-sm-4 refereshrecapcha">
									{!! captcha_img('flat')  !!}
								</span>
							</div>

							<div class="form-group hide">
								<div class="col-md-6 col-md-offset-4">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="remember" checked> 下次自動登入
										</label>
									</div>
								</div>
							</div>

							<div class="form-group mb15">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<button type="submit" class="btn btn-primary btn-block">
										登&nbsp;&nbsp;&nbsp;&nbsp;录
									</button>
								</div>
							</div>
							<div class="form-group mb15">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<a href="{{ route('register') }}"  class="btn btn-bs btn-block">
										免 费 注 册
									</a>
								</div>
							</div>
							<div class="form-group mb15">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<a  href="#" onclick="window.top.location.href='{{ route('forgot') }}';" class="btn btn-gary btn-block">
										忘 记 密 码
									</a>
								</div>
							</div>
							<div class="form-group hide">
								<div class="col-md-4 col-sm-4 col-xs-4 col-md-offset-2 col-sm-offset-2 col-xs-offset-2 padding-0 text-left">
									<a class="btn btn-link" href="{{ route('forgot') }}">忘记密码</a>
								</div>
								<div class="col-md-4 col-sm-4 col-xs-4 padding-0 text-right">
									<a class="btn btn-link" href="{{ route('register') }}">注册会员</a>
								</div>
							</div>




						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

$(function (){

	$('.refereshrecapcha').click(function (){
		//$('.captcha-img').attr('src',api.checkCode());
		$.ajax({
			url: '/login/refereshcapcha',
			type: 'get',
			dataType: 'html',        
			success: function(json) {
				$('.refereshrecapcha').html(json);
			},
			error: function(data) {
				alert('Try Again.');
			}
		});	
	});

	$('#MasterForm').submit(function( event ) {
		var data = {};
		$.map( $(this).serializeArray(), function(n, i){
			data[n['name']] = n['value'];
		});
		var login =	av.login(data.name, data.password, data.captcha);

		event.preventDefault();
		return false;
	});


});
</script>
@endsection