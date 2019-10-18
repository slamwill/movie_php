<?php

namespace App\Console\Jobs;


use Illuminate\Support\Facades\Redis;

class SyncDiyizy8
{

	public function attr($object, $attribute) {
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
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
			$xml = file_get_contents('https://www.diyizy8.com/inc/api.php?ac=videolist&h=24');
//			$xml = file_get_contents('https://www.diyizy8.com/inc/api.php?ac=videolist&pg=49');
			
			$this->doSync($xml);
		//}
		echo time();
		exit;
	

	}






	public function doSync($xml){

		


		$tagMap = [
			1 => ['人妻','熟女'],	
			5 => ['丝袜','制服'],	
			6 => ['美颜','中出'],	
			7 => ['中文'],	
			8 => ['中文'],	
			9 => ['自拍','偷拍'],	
			10 => ['网红主播'],	
			11 => ['国产换妻'],	
			12 => ['欧美'],
			13 => ['成人动漫'],
			2 => ['经典三级'],
			14 => ['开放90后'],
			15 => ['手机视频'],
			3 => ['站长推荐'],
		
		];

		foreach ($tagMap as $arr){
			foreach ($arr as $row){
				$result = \App\AvTag::where('name', 'like', '%'.$row.'%')->get()->pluck('id')->toArray();
				if (!$result) {
					$AvTag = new \App\AvTag;
					$AvTag->name = $row;
					$AvTag->name_cn = $row;
					$AvTag->save();				
				}
			}

		}


		//print_r($AvTag);


	


		$xml = simplexml_load_string($xml,null,LIBXML_NOCDATA);
		$videos = $xml->list->video;
		foreach ($videos as $video){
			$tid = (array)$video->tid;
			$tid = $tid[0];



			$AvTag = \App\AvTag::query();

			foreach($tagMap[$tid] as $word){
				$AvTag->orWhere('name', 'LIKE', '%'.$word.'%');
			}
			$AvTagArray = $AvTag->distinct()->get()->pluck('id')->toArray();
			if (!$AvTagArray) {
				$AvTagArray = [];		

				foreach($tagMap[$tid] as $word){
					$AvTag = new \App\AvTag;
					$AvTag->name = $word;
					$AvTag->name_cn = $word;
					$AvTag->save();
					$AvTagArray[] = $AvTag->id;		
				}
			}




			
			
			//香港三級片不抓
			//if ($video->tid == 2) continue;
			//print_r($video);
			$avkey = "FREE-{$video->id}";		
			$AvVideo = \App\AvVideo::updateOrCreate(
				['avkey' => $avkey]
			);
			$AvVideo->title = $video->name;
			$AvVideo->origin_cover = $video->pic;

			$m3u8_url = explode('$',$video->dl->dd[0])[1];
			$mp4_url = explode('$',$video->dl->dd[1])[1];

			$AvVideo->m3u8_url = $m3u8_url;
			$AvVideo->mp4_url = $mp4_url;


			$video_type = 0;
			$video_source = 0;

			
			if (in_array($video->tid,[1,2,5,6,7,8])) {
				$video_source = 0;
			}
			if (in_array($video->tid,[12])) {
				$video_source = 1;
			}
			if (in_array($video->tid,[13])) {
				$video_source = 2;
			}
			if (in_array($video->tid,[9,10,11,14,15,3])) {
				$video_source = 3;
			}
			//if (in_array($video->tid,[12])) {
			////	$video_source = 1;
			//}
			if (in_array($video->tid,[13])) {
				//$video_source = 2;//動漫
				$video_type = 1;
			}
			//if (in_array($video->tid,[3,9,10,11,14,15])) {
			//	$video_source = 3;//自拍
			//}

			if (in_array($video->tid,[8])) {
				$video_type = 1;
			}

			$AvVideo->release_date = $video->last;
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


/*
	public function doSync($xml){
		$xml = simplexml_load_string($xml,null,LIBXML_NOCDATA);
		$videos = $xml->list->video;
		foreach ($videos as $video){
			//香港三級片不抓
			if ($video->tid == 2) continue;
			//print_r($video);
			$avkey = "AV-{$video->id}";		
			$AvVideo = \App\AvVideo::updateOrCreate(
				['avkey' => $avkey]
			);
			$AvVideo->title = $video->name;
			$AvVideo->origin_cover = $video->pic;

			$m3u8_url = explode('$',$video->dl->dd[0])[1];
			$mp4_url = explode('$',$video->dl->dd[1])[1];

			$AvVideo->m3u8_url = $m3u8_url;
			$AvVideo->mp4_url = $mp4_url;


			$video_type = 0;
			$video_source = 0;
			if (in_array($video->tid,[12])) {
				$video_source = 1;
			}
			if (in_array($video->tid,[13])) {
				$video_source = 2;//動漫
				$video_type = 1;
			}
			if (in_array($video->tid,[3,9,10,11,14,15])) {
				$video_source = 3;//自拍
			}

			if (in_array($video->tid,[8])) {
				$video_type = 1;
			}

			$AvVideo->release_date = $video->last;
			$AvVideo->has_video = 1;
			$AvVideo->video_source = $video_source;
			$AvVideo->video_type = $video_type;
			//if (!$AvVideo->enable) $AvVideo->enable = 'off';
			if (!$AvVideo->enable) $AvVideo->enable = 'on';

			$AvVideo->save();
		}
	
	}
*/
}
