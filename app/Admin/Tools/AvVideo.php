<?php

namespace App\Admin\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class AvVideo extends AbstractTool
{
    protected function script()
    {
        $url = Request::url();
		//$url = Request::fullUrlWithQuery();

		$video_source = Request::get('video_source') ? Request::get('video_source') : 'all';
		$video_type = Request::get('video_type') ? Request::get('video_type') : 'all';
		$has_video = Request::get('has_video') ? Request::get('has_video') : 'all';
		$is_free = Request::get('is_free') ? Request::get('is_free') : 'all';
		$enable = Request::get('enable') ? Request::get('enable') : 'all';

		return <<<EOT

var VideoRadio = {
	'video_source' : '$video_source',
	'video_type' : '$video_type',
	'has_video' : '$has_video',
	'is_free' : '$is_free',
	'enable' : '$enable',
};
$('input:radio.tools-video-source').change(function () {

	VideoRadio['video_source'] = $(this).val();
	var url = "$url?"+$.param(VideoRadio);
	$.pjax({container:'#pjax-container', url: url });

});

$('input:radio.tools-video-type').change(function () {

	VideoRadio['video_type'] = $(this).val();
	var url = "$url?"+$.param(VideoRadio);
	$.pjax({container:'#pjax-container', url: url });

});

$('input:radio.tools-has-video').change(function () {

	VideoRadio['has_video'] = $(this).val();
	var url = "$url?"+$.param(VideoRadio);
	$.pjax({container:'#pjax-container', url: url });

});
$('input:radio.tools-is-free').change(function () {

	VideoRadio['is_free'] = $(this).val();
	var url = "$url?"+$.param(VideoRadio);
	$.pjax({container:'#pjax-container', url: url });

});

$('input:radio.tools-enable').change(function () {

	VideoRadio['enable'] = $(this).val();
	var url = "$url?"+$.param(VideoRadio);
	$.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        $video_source = [
            'all'   => '全部',
            '1'     => '亚洲',
            '2'     => '歐美',
            '3'     => '卡通',
            '4'     => '短片',
        ];
        $video_type = [
            'all'   => '全部',
            '1'     => '無碼',
            '2'     => '有碼',
        ];

		$has_video =[
            'all'   => '全部',
            '2'     => '有影片',
            '1'     => '無影片',
		];

		$enable =[
            'all'   => '全部',
            'on'    => '上架',
            'off'   => '下架',
		];
		
		$is_free =[
            'all'   => '全部',
            '1'    => '付費',
            '2'   => '免費',
		];
		
        return view('admin.tools.AvVideo', 	[
			'video_source' => $video_source,
			'video_type' => $video_type,
			'has_video' => $has_video,
			'is_free' => $is_free,
			'enable' => $enable
		]);
    }
}