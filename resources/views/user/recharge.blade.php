@extends('layouts.users')


@section('content')
<div class="row">
	<h4 class="head-title">会员充值</h4>
<style>

</style>


	<div class="row recharge-box">
		@foreach ($RechargeConfig as $row)
			<div class="col-xs-6 col-sm-3">
				<div class="cash-package {{ !$loop->index ? 'active' : '' }}" data-recharge-id="{{$row['id']}}" data-coins="{{ $row['coins'] }}" data-amount="{{ $row['amount'] }}">
					<span class="unit">$</span><span class="num">{{ $row['amount'] }}</span> {{ config('av.currency')[$row['currency']] }} / <span class="coins">{{ $row['coins'] }}</span> 点
					<div><span class="tag">{{ $row['description'] }}</span></div>
				</div>
			</div>
		@endforeach
	</div>

	<div id="recharge-tabs" class="recharge-box">
		<ul class="tab-title">
			<li class="active"><a href="javascript:void(0);" data-target="#webchat"><span class="fab fa-cc-amazon-pay"></span> 支付渠道</a></li>
			<li style="display:none;"><a href="javascript:void(0);" data-target="#alipay"><span class="fab fa-alipay fa-lg"></span>___</a></li>
		</ul>
		<div id="webchat" class="tab-inner">



		<form method="POST" action="{{ url()->current() }}" accept-charset="UTF-8" class="form-horizontal" data-pjax=".right-box" id="happyform">
			{{ csrf_field() }}
			<input type="hidden" name="payment" value="" class="payment">
			<input type="hidden" name="rechargeId" value="1" class="rechargeId">
			<div class="form-group">
				<label class="control-label col-sm-2 col-xs-3">支付金额</label>
				<div class="col-sm-10 col-xs-9">
					<p class="form-control-static" style="padding-top:6px">
						<span class="amount">10.00</span> 元
					</p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2 col-xs-3">获得点数</label>
				<div class="col-sm-10 col-xs-9">
					<p class="form-control-static" style="padding-top:6px">
						<span class="coins">10</span> 点
					</p>
				</div>
			</div>
			{{--
			<div class="form-group">
				<label class="control-label col-sm-2 hidden-xs">选择渠道</label>
				<div class="col-sm-10 ">
					<button class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="PD-EPOINT-ALIPAY" data-form="happyform"><span class="fab fa-alipay "></span> 支付寶扫码</button>
					<button class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="PD-EPOINT-WECHAT" data-form="happyform"><span class="fab fa-weixin "></span> 微信扫码</button>
					<button class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="PD-CREDIT-CHINAPAY" data-form="happyform"><i class="fas fa-money-check-alt "></i> 银联卡付款</button>
					<button class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="PD-CREDIT-CHINAPAY-TWD" data-form="happyform"><i class="fas fa-money-check-alt "></i> 银联卡付款（台币计价优惠费率0.2）</button>
				</div>
			</div>
			--}}


		
		</form>


		<form method="POST" action="{{ route('user.bfRecharge') }}" accept-charset="UTF-8" class="form-horizontal" data-pjax=".right-box" id="bfform">
			{{ csrf_field() }}
			<input type="hidden" name="payment" value="" class="payment">
			<input type="hidden" name="rechargeId" value="1" class="rechargeId">
			<div class="form-group">
				<label class="control-label col-sm-2 hidden-xs">选择渠道1</label>
				<div class="col-sm-10 ">
					<button type="button" class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="18" data-form="bfform"><span class="fab fa-alipay "></span> 支付宝</button>
					{{-- <button type="button" class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="13" data-form="bfform"><span class="fab fa-weixin "></span> 微信扫码</button> --}}
				</div>
			</div>
		</form>

		{{--
		<form method="POST" action="{{ route('user.nnexRecharge') }}" accept-charset="UTF-8" class="form-horizontal" data-pjax=".right-box" id="nexxform">
			{{ csrf_field() }}
			<input type="hidden" name="payment" value="" class="payment">
			<input type="hidden" name="rechargeId" value="1" class="rechargeId">
			<div class="form-group">
				<label class="control-label col-sm-2 hidden-xs">选择渠道2</label>
				<div class="col-sm-10 ">
					<button type="button" class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="930" data-form="nexxform"><span class="fab fa-alipay "></span> 支付宝</button>
					<button type="button" class="btn btn-primary btn-block-mobile-only payment-btn" data-payment="929" data-form="nexxform"><span class="fab fa-alipay "></span> 云闪付</button>
				</div>
			</div>
		</form>
		--}}




		</div>
		<div id="alipay" class="tab-inner" style="display:none">
			<p></p>
		</div>

	</div>


</div>
<script type="text/javascript">

$(function (){

	$('.payment-btn').click(function (){
		$('.payment').val($(this).data('payment'));
		console.log($(this).data('form'));
		$('#' + $(this).data('form')).submit();
		//return false;
	});
	$('.cash-package').click(function (){
		var data = $(this).data();
		$('.rechargeId').val(data.rechargeId);
		$('#recharge-tabs .amount').html(data.amount);
		$('#recharge-tabs .coins').html(data.coins);
		$('.cash-package').removeClass('active');
		$(this).addClass('active');
	});
	$('.cash-package').eq(0).click();
	$('#recharge-tabs ul.tab-title li').click(function(){
		$($(this).find('a').data('target')).show().siblings('.tab-inner').hide();
		$(this).addClass('active').siblings ('.active').removeClass('active');
	});
});

</script>

@endsection
