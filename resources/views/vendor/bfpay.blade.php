<div class="no-record">
	<img src="{{ url('images/user/emptyFileIcons.png') }}">
	<div style="color:#00be06">跳转中...请稍候...</div>
</div>

<form id="form" method="get" accept-charset="UTF-8" action="https://api.bfbaopay.com/bifubao-gateway/front-pay/h5-pay.htm">
@foreach ($params as $key => $val)
	<input type="hidden" name="{{ $key }}" value="{{ $val }}" />
@endforeach

</form>
<script type="text/javascript">
document.getElementById("form").submit();
</script>
