@if(env('MINIFY_JS'))
<script src="{{ asset('dist/all.min.js?v='.env('CSS_JS_VERSION')) }}"></script>
@else
<script src="{{ asset('js/app.js') }}"></script>
{{--
<!--
<script src="{{ asset('js/slidebarsV2.js') }}"></script>
<script src="{{ asset('js/zeroModal.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/jquery.smartWizard.min.js') }}"></script>
<script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/jquery.twbsPagination.min.js') }}"></script>
<script src="{{ asset('js/jquery.blockUI.min.js') }}"></script>
<script src="{{ asset('js/jsencrypt.js') }}"></script>
<script src="{{ asset('js/pcas-class.js') }}"></script>
<script src="{{ asset('js/jquery.rwd.tabs.js') }}"></script>
<script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('js/jquery.qrcode.min.js') }}"></script>
<script src="{{ asset('js/clipboard.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-toggle.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.12.2/videojs-contrib-hls.min.js"></script>
-->
--}}

<script src="{{ asset('js/swiper.min.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.min.js') }}"></script>

<script src="{{ asset('js/app.model.js') }}"></script>

<script src="{{ asset('js/api.model.js') }}"></script>
{{--
<script src="{{ asset('js/api.config.js') }}"></script>
--}}
<script src="{{ asset('js/av.model.js') }}"></script>
<script src="{{ asset('js/progress.js') }}"></script>
<script src="{{ asset('js/jquery.pjax.min.js') }}"></script>
<script src="{{ asset('js/nprogress.js') }}"></script>

<script src="{{ asset('js/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('js/iscroll.min.js') }}"></script>
<!-- drawer.js -->
<script src="{{ asset('js/drawer.min.js') }}"></script>
<!-- Flowplayer-->
<script src="{{ asset('js/hls.min.js') }}"></script>
<script src="{{ asset('js/flowplayer.min.js') }}"></script>
<!-- hls.js for flowplayer -->

@endif
{{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.5.0/video.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.5.0/lang/zh-TW.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.5.0/lang/zh-CN.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.15.0/videojs-contrib-hls.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-flash/2.1.0/videojs-flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-hotkeys/0.2.20/videojs.hotkeys.min.js"></script>

--}}
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.6.0/ie8/videojs-ie8.min.js"></script>
-->


<script src="{{ asset('plugin/layer/3.1.0/layer.js') }}"></script>
