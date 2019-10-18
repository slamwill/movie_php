<?php

namespace App\Console\Jobs;


use Illuminate\Support\Facades\Redis;

class Sync4hu
{

	public function sync(){
/*
		$AvTag = \App\AvTag::where('name','like', '%中出34%')->get();
		var_dump($AvTag->toArray() ? 1 : 0);
		print_r($AvTag->toArray());
		exit;
*/
		//for ($i=50; $i>=1; $i--){
			//$i=27;
		//	$xml = file_get_contents('https://www.diyizy8.com/inc/api.php?ac=videolist&pg='.$i);
			$json = file_get_contents('http://68.168.141.87:8081/index.php?h=24');
	//		$json = file_get_contents('/tmp/4hu.json');

//			$xml = file_get_contents('https://www.diyizy8.com/inc/api.php?ac=videolist&pg=49');
			
			$this->doSync($json);
		//}
		echo date('Y-m-d H:i:s',time());
		exit;
	

	}


	public function checkTag($tag) {
	
		$result = \App\AvTag::where('name', 'like', '%'.$tag.'%')->get()->pluck('id')->toArray();
		if (!$result) {
			$AvTag = new \App\AvTag;
			$AvTag->name = $tag;
			$AvTag->name_cn = $tag;
			$AvTag->save();				
		}

	}
/*
4hu分類
Array
(
    [0] => Array
        (
            [list_name] => 熟女人妻
            [list_id] => 111
        )

    [1] => Array
        (
            [list_name] => 美颜巨乳
            [list_id] => 112
        )

    [2] => Array
        (
            [list_name] => S级女优
            [list_id] => 100
        )

    [3] => Array
        (
            [list_name] => 成人动漫
            [list_id] => 101
        )

    [4] => Array
        (
            [list_name] => SM系列
            [list_id] => 108
        )

    [5] => Array
        (
            [list_name] => 经典三级
            [list_id] => 109
        )

    [6] => Array
        (
            [list_name] => 换妻游戏
            [list_id] => 90
        )

    [7] => Array
        (
            [list_name] => 网红主播
            [list_id] => 91
        )

    [8] => Array
        (
            [list_name] => 明星艳照门
            [list_id] => 92
        )

    [9] => Array
        (
            [list_name] => 开放90后
            [list_id] => 93
        )

    [10] => Array
        (
            [list_name] => 波多野结衣
            [list_id] => 94
        )

    [11] => Array
        (
            [list_name] => 吉泽明步
            [list_id] => 95
        )

    [12] => Array
        (
            [list_name] => 苍井空
            [list_id] => 96
        )

    [13] => Array
        (
            [list_name] => 麻生希
            [list_id] => 97
        )

    [14] => Array
        (
            [list_name] => 天海翼
            [list_id] => 98
        )

    [15] => Array
        (
            [list_name] => 欧美性爱
            [list_id] => 62
        )

    [16] => Array
        (
            [list_name] => 夫妻同房
            [list_id] => 87
        )

    [17] => Array
        (
            [list_name] => VR虚拟现实
            [list_id] => 86
        )

    [18] => Array
        (
            [list_name] => 手机小视频
            [list_id] => 88
        )

    [19] => Array
        (
            [list_name] => 自拍偷拍
            [list_id] => 89
        )

    [20] => Array
        (
            [list_name] => 颜射吃精
            [list_id] => 113
        )

    [21] => Array
        (
            [list_name] => 丝袜制服
            [list_id] => 114
        )

    [22] => Array
        (
            [list_name] => 无码中字
            [list_id] => 115
        )

    [23] => Array
        (
            [list_name] => 精彩短片
            [list_id] => 116
        )

    [24] => Array
        (
            [list_name] => 中文有码
            [list_id] => 131
        )

    [25] => Array
        (
            [list_name] => 葵司
            [list_id] => 122
        )

    [26] => Array
        (
            [list_name] => 泷泽萝拉
            [list_id] => 123
        )

    [27] => Array
        (
            [list_name] => 高清无码
            [list_id] => 130
        )

    [28] => Array
        (
            [list_name] => 水菜麗
            [list_id] => 127
        )

    [29] => Array
        (
            [list_name] => 宇都宮紫苑
            [list_id] => 128
        )

)
*/
	public function doSync($json){

		
		$videos = json_decode($json, true);
		foreach ($videos as $video){

			$arrays = explode('$$$',$video['vod_url']);
			if (count($arrays) < 2) continue;
			$m3u8String = $arrays[0];
			$mp4String = $arrays[1];

			$tag = $video['list_name'];
			//檢查tag．不存在就建立
			$this->checkTag($tag);


			$AvTag = \App\AvTag::query();
			$AvTag->orWhere('name', 'LIKE', '%'.$tag.'%');

			$AvTagArray = $AvTag->distinct()->get()->pluck('id')->toArray();

			$avkey = "FREE-{$video['vod_id']}";		

			$AvVideo = \App\AvVideo::updateOrCreate(
				['avkey' => $avkey]
			);
			$AvVideo->title = $video['vod_name'];
			$AvVideo->origin_cover = $video['vod_pic'];

			$m3u8_url = explode('$',$m3u8String)[1];
			$mp4_url = explode('$',$mp4String)[1];

			$AvVideo->m3u8_url = $m3u8_url;
			$AvVideo->mp4_url = $mp4_url;


			$video_type = 0;
			$video_source = 0;

			
			if (in_array($video['list_id'],[100,111,112,87,94,97,108,109,113,114,115,130])) {
				$video_source = 0;
			}
			if (in_array($video['list_id'],[86,95,96,98,122,123,127,128,131])) {
				$video_source = 0;
				$video_type = 1;//有碼
			}
			if (in_array($video['list_id'],[62])) {
				$video_source = 1;
			}

			if (in_array($video['list_id'],[87,88,89,90,91,92,93,116])) {
				$video_source = 3;//自拍偷拍
			}

			if (in_array($video['list_id'],[101])) {
				$video_source = 2;//動漫
				$video_type = 1;
			}

			$AvVideo->release_date = date('Y-m-d',$video['vod_addtime']);
			$AvVideo->has_video = 1;
			$AvVideo->is_free = 1;
			$AvVideo->video_source = $video_source;
			$AvVideo->video_type = $video_type;
			//if (!$AvVideo->enable) $AvVideo->enable = 'off';
			if (!$AvVideo->enable) $AvVideo->enable = 'on';

			$AvVideo->save();
			if (!$AvVideo->tags) {
				//删除影片tag
				\App\AvVideoTag::where('video_id',$AvVideo->id)->delete();
				//新增影片
				foreach ($AvTagArray as $tagId){
					$AvVideoTag = new \App\AvVideoTag;
					$AvVideoTag->video_id = $AvVideo->id;
					$AvVideoTag->tag_id = $tagId;
					$AvVideoTag->save();
				}

				$AvVideo->tags = $AvTagArray;
				$AvVideo->update();
			}

		}
	
	}


}
