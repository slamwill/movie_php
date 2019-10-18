<html lang="en">
<head>
<meta charset="utf-8">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="{{ asset('js/chplayer/chplayer.min.js') }}"></script>

<title></title>
</head>
<body style="margin:0px;padding:0px;">

<div id="video" style="width:100%;height:500px;"></div>


<script type="text/javascript">
var app = {};
app.player = function (){
	var videoObject = {
		logo: '', //设置logo，非必须
		container: '#video',//“#”代表容器的ID，“.”或“”代表容器的class
		variable: 'player',//该属性必需设置，值等于下面的new chplayer()的对象
		poster: '{{ $AvVideo['origin_cover'] }}', //封面图片地址
		autoplay: true, //是否自动播放，默认true=自动播放，false=默认暂停状态
		video:'{{ $AvVideo['m3u8_url'] }}',
	};
	var player = new chplayer(videoObject);
	
	var resize = function (){
		var video = $("#video");
		video.height(video.width()/16*9);
	};
	$( window ).resize(function() {
		resize();
	});
	resize();
}
$(function (){
	app.player();
});
</script>
</body>
</html>
