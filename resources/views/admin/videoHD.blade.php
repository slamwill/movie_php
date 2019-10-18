<html lang="en">
<head>
<meta charset="utf-8">

<link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.4.0/video-js.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.4.0/video.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.4.0/lang/zh-TW.js"></script>
<script src="https://unpkg.com/videojs-flash/dist/videojs-flash.js"></script>
<script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>

  <!-- If you'd like to support IE8 -->
<script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>

<title>videojs</title>
</head>
<body style="margin:0px;padding:0px;">

  
<video id="video" class="video-js vjs-default-skin vjs-16-9" controls preload="auto"  data-setup='{ "fluid": true ,"language" : "zh-TW" }'>
	<source src="{{ route('api.admin.play',[$AvVideo['avkey']]) }}" type="application/x-mpegURL">
</video>

<script>

$(function (){


	var player = videojs('video', {
//		'controls': true,
//		'preload' : 'auto',
		'techOrder': ['html5'],

		
		
	},function (){
      var player = this;
      window.player = player

	}); 

	player.ready(function() {
		this.play();
	});


});

</script>
</body>
</html>
