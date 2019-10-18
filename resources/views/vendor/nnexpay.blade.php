<div class="no-record">
	<img src="{{ url('images/user/emptyFileIcons.png') }}">
	<div style="color:#00be06">跳转中...请稍候...</div>
	<form id="form" class="form-inline" method="post" action="<?php echo "http://all.nn-ex.com/Pay_Index.html"; ?>">
	@foreach ($params as $key => $val)
		<input type="hidden" name="{{ $key }}" value="{{ $val }}" />
	@endforeach
		<button type="submit" class="btn btn-success btn-lg">扫码支付(金额：<?php echo $params["pay_amount"]; ?>元)</button>
	</form>
</div>

<script type="text/javascript">
// document.getElementById("form").submit();
</script>
