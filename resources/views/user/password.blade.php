@extends('layouts.users')
@section('pageTitle', '修改登录密码')

@section('content')



<div class="tab-content margin-top-15">

<form method="POST" action="{{ url()->current() }}" accept-charset="UTF-8" class="form-horizontal" id="MasterForm" data-pjax=".right-box">
	{{ csrf_field() }}
	<div class="form-group">
		<label for="input-password1" class="control-label col-sm-2">当前登录密码</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="input-password1" name="curPassword" placeholder="请输入当前登录密码" required="required" value="{{ old('curPassword') }}" maxlength="20">
		</div>
	</div>
	<div class="form-group">
		<label for="input-password2" class="control-label col-sm-2">新登录密码</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="input-password2" name="newPassword" placeholder="请输入新登录密码" required="required" value="{{ old('newPassword') }}" maxlength="20">
		</div>
	</div>
	<div class="form-group">
		<label for="input-password3" class="control-label col-sm-2">确认登录密码</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="input-password3" name="newPassword_confirmation" placeholder="确认登录密码" required="required" value="{{ old('newPassword_confirmation') }}" maxlength="20">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			<button type="submit" class="btn btn-primary btn-block-mobile-only">確定修改</button>
		</div>
	</div>
</form>

</div>

<script type="text/javascript">

$('#MasterForm').submit(function( event ) {

	return true;
	//event.preventDefault();
});
</script>

@endsection
