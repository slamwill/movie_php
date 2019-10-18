<div class="no-record">
	<img src="{{ url('images/user/emptyFileIcons.png') }}">
	<div style="color:#00be06">跳转中...请稍候...</div>
</div>

<form id="form" method="post" accept-charset="UTF-8" action="https://api.55168957.com/paysel_amt.php">
@foreach ($params as $key => $val)
	<input type="hidden" name="{{ $key }}" value="{{ $val }}" />
@endforeach

</form>
<script type="text/javascript">
document.getElementById("form").submit();
</script>
