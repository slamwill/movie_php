@extends('layouts.app')
@section('content')
<style>
footer {
	display: none;
}

</style>
<div class="girl-bg2"></div>

<div class="login-box ">

<div class="container">
    <div class="row">
			<div class="col-lg-4 col-md-6 col-md-offset-3 col-lg-offset-4">


			<div class="panel panel-default">


                <div class="panel-body">
										<div class="title">注 册 会 员</div>
                    <form class="form-horizontal" id="MasterForm" method="POST" action="">
                        {{ csrf_field() }}


                        <div class="form-group">
							<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
								<label for="name" class="col-md-3 col-xs-3 control-label form-control-static">帐号</label>

								<div class="col-md-9 col-xs-9 padding-0">
								    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="6-12英文字母及数字组合">
								</div>
							</div>
                        </div>

                        <div class="form-group">
							<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
								<label for="password" class="col-md-3 col-xs-3 control-label form-control-static">密码</label>

								<div class="col-md-9 col-xs-9 padding-0">
									<input id="password" type="password" class="form-control" name="password" required placeholder="6-12英文字母及数字组合">
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
							<div class="col-md-11 col-md-offset-1 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1 padding-0 say-yes">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="say-yes">
										我已届满合法博彩年齡，且同意各项<a href="/help?id=13" target="_blank">条款</a>
									</label>
								</div>
                            </div>
                        </div>

						<div class="form-group mb15">
							<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
                                <button type="submit" class="btn btn-primary btn-block">
                                    确 定 注 册
                                </button>
                            </div>
                        </div>
							<div class="form-group ">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<a href="{{ route('login') }}"  class="btn btn-bs btn-block">
										会 员 登 录
									</a>
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
		if (data.name.length < 6) {
			app.error('帐号最少六个字元');			
			return false;
		}
		if (data.password.length < 6) {
			app.error('密码最少六个字元');			
			return false;
		}

		if (!data.captcha) {
			app.error('请输入验证码');			
			return false;
		}

		var register = av.register(data.name, data.password, data.captcha);
		if (register.status == 1) {
			app.success({ content : '注册成功' , yes : function (index){
				layer.close(index);
				window.top.location.href = '{{ route('latest') }}';
			}});
		}
		else {
			$.each(register.errors,function (key,value){		
				app.error(value[0]);
				return false;
			});
		}
		event.preventDefault();
		return false;


	});


});
</script>
@endsection