@extends('layouts.users')


@section('content')
<div class="row">
	<h4 class="head-title">个人设置</h4>
	<div class="panel user-info">
		<form class="form-horizontal">


			<div class="form-group">
				<label class="control-label col-sm-2 col-xs-3">会员账号</label>
				<div class="col-sm-10 col-xs-9"><p class="form-control-static">{{ Auth::User()->name }}</p></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2 col-xs-3">目前点数</label>
				<div class="col-sm-10  col-xs-9"><p class="form-control-static">{{ Auth::User()->coins }} 点 <span class="recharge-tag" onclick="$('#recharge').click();" data-toggle="tooltip" title="点数不够?立即充值">充值</span></p></div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2  col-xs-3">会员状态</label>
				<div class="col-sm-10 col-xs-9"><p class="form-control-static">
					@if (Auth::User()->expired && Auth::User()->expired > date('Y-m-d H:i:s'))
						VIP会员  <span class="gary-text">( {{Auth::User()->expired}} 到期 )</span>
					@else
						免费会员
						@if (Auth::User()->expired)
							<span class="gary-text">( {{Auth::User()->expired}} 到期 )</span>
						@endif 
						<span class="level-up" onclick="$('#vip').click();" data-toggle="tooltip" title="升级VIP全站无限看">升级VIP</span>
						
					@endif

					</p>
				</div>
			</div>
			<div class="form-group form-inline">
				<label class="control-label col-sm-2" style="padding-top:7px;">邮箱</label>
				<div class="col-sm-10">
					
					<p class="form-control-static" style="width:100%">
						{{  Auth::User()->email }} <a href="javascript:void(0);" class="new-mail pull-right">修改</a> <i class="fas fa-question-circle" data-toggle="tooltip" title="请输入邮箱，以便密码遗失可透过箱重新设定"></i>
					</p>
					<div class="mail-input" style="display:none;">

							<input type="text" class="form-control email" name="email" placeholder="请输入邮箱" required="required" value="{{  Auth::User()->email }}" maxlength="100">

							<span class="save-btn">保存</span>

					</div>
				</div>
			</div>
		</form>

	</div>



	<h4 class="head-title">修改密码</h4>
	<div class="panel">
		<form method="POST" action="{{ url()->current() }}" accept-charset="UTF-8" class="form-horizontal" id="MasterForm" data-pjax=".right-box">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="input-password1" class="control-label col-sm-2">当前密码</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="input-password1" name="curPassword" placeholder="当前密码" required="required" value="{{ old('curPassword') }}" maxlength="20">
				</div>
			</div>
			<div class="form-group">
				<label for="input-password2" class="control-label col-sm-2">新密码</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="input-password2" name="newPassword" placeholder="新密码" required="required" value="{{ old('newPassword') }}" maxlength="20">
				</div>
			</div>
			<div class="form-group">
				<label for="input-password3" class="control-label col-sm-2">确认新密码</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="input-password3" name="newPassword_confirmation" placeholder="确认新密码" required="required" value="{{ old('newPassword_confirmation') }}" maxlength="20">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-10 col-sm-offset-2">
					<button type="submit" class="btn btn-primary btn-block-mobile-only">確定修改</button>
				</div>
			</div>
		</form>

	</div>


</div>
<script type="text/javascript">
$('.new-mail').click(function (){
	$(this).parent('p').hide();
	$('.mail-input').show();
});

$('.save-btn').unbind().click(function (){
	//$('#EmailForm').submit();

	var email = $('.email').val();
	
	$.ajax({
		url: '/user/emailUpdate',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		//contentType:'application/json; charset=utf-8',
		cache : false,
		data: { 'email' : email},
		//dataType:'json',
		async: false,
		success: function (response) {
			if (response.status == 0) {
				app.error(response.message);
			}
			else {
				app.success(response.message);
				$('#personal').click();
			}
		},
		error: function(response){
		}
	});


});
</script>

@endsection
