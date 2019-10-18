@extends('layouts.users')


@section('content')
<div class="row">
	<h4 class="head-title">交易记录</h4>

@if (!$TransferLog->count())
	<div class="no-record">
		<img src="{{ url('images/user/emptyFileIcons.png') }}">
		<div>这里暂时没有任何交易记录</div>
	</div>
@else
<div class="tab-content">

	<div class="table-responsive">
		<table class="table table-hover table-bordered">
		  <thead>
			<tr>
			  <th scope="col" class="text-center" width="110">類型</th>
			  <th scope="col" class="text-center hidden-xs"  width="110">交易时间</th>
			  <th scope="col" class="text-center">名称｜交易编号</th>
			  <th scope="col" class="text-center" width="110">点数</th>
			  <th scope="col" class="text-center hidden-xs" width="110">当前馀额</th>
			  <th scope="col" class="text-center">操作</th>
			</tr>
		  </thead>
		  <tbody>

		@foreach ($TransferLog as $rows)
			<tr>
			  <td class="text-center align-middle">{{ config('av.transfer_log')[$rows->type]  }}</td>
			  <td class="hidden-xs align-middle">{{ explode(' ',$rows->created_at)[0] }}<div class="gary-text">{{ explode(' ',$rows->created_at)[1] }}</div></td>
			  <td class="align-middle">
				@if ($rows->type == 3)
					影片 [ <a href="{{ route('watch', [ $rows->memo ] ) }}" target="_blank">{{ $rows->memo }}</a> ]
				@else
					{{ $rows->memo }}
				@endif
				@if ($rows->type == 2)
					
				@elseif ($rows->type == 3)
					<span class="gary-text" style="color:yellow">{{ $rows->json['expired'] }}到期</span>
				@endif
				<div class="gary-text">交易编号{{ $rows->order_no }}</div>
			  </td>
			  <td class="text-center align-middle"><span class="{{ $rows->coins <= 0 ? 'red-tag' : 'blue-tag' }}">{{ $rows->coins > 0 ? '+' : '' }}{{ $rows->coins }} </span></td>
			  <td class="hidden-xs text-center align-middle">{{ $rows->user_coins }}</td>
			  <td class="text-center align-middle">
				<a href="javascript:void(0);" class="detail" data-index="{{$loop->index}}">详情</a>
			  </td>
			</tr>
			
		@endforeach


		  </tbody>
		</table>
	</div>
	<center>
		{{-- $TransferLog->links() --}}
{{-- 自定義分頁--}}
		{!! $TransferLog->render('layouts/paginator') !!}

	</center>

</div>

@endif

</div>
<style>
.detail-box {
	font-size:12px;
	color:#777;
	line-height:26px;
}
.detail-box .col-xs-3{
	padding:0;
	text-align:right;
letter-spacing: 1px;
}
.detail-box .col-xs-9{
	padding:0;
	text-align:left;
}


</style>
<script type="text/javascript">
var detail = @json($TransferLog);
$('.detail').unbind().click(function (){

	var index = $(this).data('index');

	var data = detail.data[index];
	var content = '<div class="detail-box">';

	if (data.type == 3){

			content +='<div class="row"><div class="col-xs-3">交易名称：<\/div><div class="col-xs-9">下载影片<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">交易编号：<\/div><div class="col-xs-9">' + data.order_no + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">影片番号：<\/div><div class="col-xs-9">' + data.memo + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">会员点数：<\/div><div class="col-xs-9">' + data.json.coins.from + ' <i class="fas fa-arrow-right"><\/i> ' + data.json.coins.to + ' ( -'+data.coins+' )<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">下载期限：<\/div><div class="col-xs-9">' + data.json.expired + '到期<\/div><\/div>';
			content +='<\/div>';

	
	}
	else {

		if (data.json.transfer)	{
		//	console.log(data.json);
			content +='<div class="row"><div class="col-xs-3">交易名称：<\/div><div class="col-xs-9">' + data.memo + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">交易编号：<\/div><div class="col-xs-9">' + data.order_no + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">支付方式：<\/div><div class="col-xs-9">' + data.json.transfer.payment + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">充值金额：<\/div><div class="col-xs-9">' + data.json.transfer.amount  + ' ' + data.json.transfer.currency + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">会员点数：<\/div><div class="col-xs-9">' + data.json.coins.from + ' <i class="fas fa-arrow-right"><\/i> ' + data.json.coins.to + ' ( +'+data.coins+' )<\/div><\/div>';
			content +='<\/div>';
		}
		else {

			content +='<div class="row"><div class="col-xs-3">交易名称：<\/div><div class="col-xs-9">' + data.memo + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">交易编号：<\/div><div class="col-xs-9">' + data.order_no + '<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">会员点数：<\/div><div class="col-xs-9">' + data.json.coins.from + ' <i class="fas fa-arrow-right"><\/i> ' + data.json.coins.to + ' ( -'+data.coins+' )<\/div><\/div>';
			content +='<div class="row"><div class="col-xs-3">VIP时间：<\/div><div class="col-xs-9">' + data.json.expired.old_expired + ' <i class="fas fa-arrow-right"><\/i> ' + data.json.expired.new_expired + '<\/div><\/div>';
			content +='<\/div>';
		}

	}
	layer.open({
	  type: 1,
	  title : '详情信息',
	  skin: 'layui-layer-rim',
	  area: ['365px', '210px'],
	  content: content
	});
});
</script>

@endsection
