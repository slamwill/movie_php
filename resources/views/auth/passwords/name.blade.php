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
						<div class="title">找 回 密 码</div>
						@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
						@endif
						<form class="form-horizontal" method="GET" action="{{ route('sendResetLinkEmail') }}">
							{{ csrf_field() }}

							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<div class="input-text col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
									<label for="name" class=" col-md-3 col-xs-3 control-label form-control-static">帐号</label>

									<div class="col-md-9 col-xs-9 padding-0">
										<input id="name" type="name" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="输入帐号">
										@if ($errors->has('name'))
											<span class="help-block hide">
												<strong>{{ $errors->first('name') }}</strong>
											</span>
										@endif
									</div>
								</div>
							</div>


							<div class="form-group mb15">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 padding-0">
									<button type="submit" class="btn btn-primary btn-block">
										寄送重置密码信件
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


<script type="text/javascript">

</script>
@endsection