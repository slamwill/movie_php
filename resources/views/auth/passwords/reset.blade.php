@extends('layouts.app')
@section('content')
<style>

footer {display:none;}


</style>
<div class="girl-bg"></div>
<div class="login-box ">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6 col-md-offset-3 col-lg-offset-4">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="title">重 置 密 码</div>
						<form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
							{{ csrf_field() }}

							<input type="hidden" name="token" value="{{ $token }}">

							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} hide">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="name" class=" col-md-3 col-xs-3 control-label form-control-static">电子信箱</label>

									<div class="col-md-9 col-xs-9 padding-0">
										<input id="email" type="email" class="form-control" name="email" value="{{ json_decode(\Crypt::decryptString(request()->get('q')))->email }}" readonly>
										@if ($errors->has('email'))
											<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
											</span>
										@endif
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="name" class="col-md-4 col-xs-4 control-label form-control-static">帐号</label>

									<div class="col-md-8 col-xs-8 padding-0">
										<input id="name" type="text" class="form-control" name="name" value="{{ json_decode(\Crypt::decryptString(request()->get('q')))->name }}" readonly>
									</div>
								</div>
							</div>
							
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="password" class=" col-md-4 col-xs-4 control-label form-control-static">新密码</label>

									<div class="col-md-8 col-xs-8 padding-0">
										<input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required autofocus placeholder="输入新密码">
										{{--
										@if ($errors->has('password'))
											<span class="help-block">
												<strong>{{ $errors->first('password') }}</strong>
											</span>
										@endif
										--}}
									</div>
								</div>
							</div>

							<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="password-confirm" class=" col-md-4 col-xs-4 control-label form-control-static">确认新密码</label>

									<div class="col-md-8 col-xs-8 padding-0">
										<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autofocus placeholder="确认新密码">
										{{--
										@if ($errors->has('password_confirmation'))
											<span class="help-block">
												<strong>{{ $errors->first('password_confirmation') }}</strong>
											</span>
										@endif
										--}}
									</div>
								</div>
							</div>

							<div class="form-group mb15">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<button type="submit" class="btn btn-primary btn-block">
										重 设 密 码
									</button>
								</div>
							</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
