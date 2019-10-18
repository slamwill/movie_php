<?php

namespace App\Classes;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class Common
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

	}
	//手動分頁
	public static function paginate(&$items,$perPage) {
		$pageStart = intval(\Request::get('page', 1));
		$offSet = ($pageStart * $perPage) - $perPage; 
		$total = count($items);
		$items = array_slice($items, $offSet, $perPage, true);
		return new LengthAwarePaginator($items, $total, $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
	}

	public static function getAllVideoTags() {
		$getAllVideoTags = redis::get('AllVideoTags');
		if (is_null($getAllVideoTags)) {
			$getAllVideoTags = \App\AvTag::pluck('id', 'name');

			//  預防使用者第一次近來  DB沒資料  redis也沒資料  無限迴圈卡在這裡
			if(!$getAllVideoTags){
				$getAllVideoTags = [true];
			}
			redis::setex('AllVideoTags',3600, $getAllVideoTags);
			$getAllVideoTags = $getAllVideoTags->toArray();
		}
		else {
			$getAllVideoTags = json_decode($getAllVideoTags, true);
		}
		return $getAllVideoTags;
	}

	public static function getAllActorTags() {
		$getAllActorTags = redis::get('AllActorTags');
		if (is_null($getAllActorTags)) {
			$getAllActorTags = \App\AvActor::pluck('id', 'name');

			//  預防使用者第一次近來  DB沒資料  redis也沒資料  無限迴圈卡在這裡
			if(!$getAllActorTags){
				$getAllActorTags = [true];
			}
			redis::setex('AllActorTags',3600, $getAllActorTags);
			$getAllActorTags = $getAllActorTags->toArray();
		}
		else {
			$getAllActorTags = json_decode($getAllActorTags, true);
		}
		return $getAllActorTags;
	}

	public static function isMyFavoriteVideos($AvVideo) {
		if (!Auth::check()) return false;
		$key = 'Favorite-'.Auth::User()->id;
		$AvUserVideo = redis::get($key);

		if (is_null($AvUserVideo)) {
			$AvUserVideo = \App\AvUserVideo::where(['user_id' => Auth::user()->id ])->pluck('id','video_id');

			//  預防使用者第一次近來  DB沒資料  redis也沒資料  無限迴圈卡在這裡
			if(!$AvUserVideo){
				$AvUserVideo = [true];
			}
			redis::setex($key,84600, $AvUserVideo);
		}
		else {
			$AvUserVideo = json_decode($AvUserVideo, true);
		}

		if(isset($AvUserVideo[$AvVideo['id']])) {
			// echo 'yes';  // 回傳到blade 顯示已加入我的最愛
			return true;
		}
		else {
			// echo 'no';  // 回傳到blade 顯示已移除我的最愛
			return false;
		}
	}

	public static function getVideo($avkey) {
		$key = 'AvVideo-'.$avkey;
		$AvVideo = Redis::get($key);

		if (is_null($AvVideo)) {

			$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$avkey)->first();
			$AvVideo = $AvVideo ? $AvVideo->toArray() : array();
			if ($AvVideo) {
				/*
				if (isset($AvVideo['duration']) && substr($AvVideo['duration'],0,2) == '00') {
					$AvVideo['_duration'] = substr($AvVideo['duration'],3);
				}
				else{
					$AvVideo['_duration'] = $AvVideo['duration'];
				}*/
				$AvVideo['actors_name'] = [];
				if (isset($AvVideo['actors'])) {
					$AvVideo['actors_name'] = \App\AvActor::whereIn('id',explode(',', $AvVideo['actors']))->get()->pluck('name','id')->toArray();
				}
				$AvVideo['tags_name'] = [];
				if (isset($AvVideo['tags'])) {
					$AvVideo['tags_name'] =	\App\AvTag::whereIn('id',explode(',', $AvVideo['tags']))->get()->pluck('name','id')->toArray();
				}
			}

			Redis::setex($key, 3600, json_encode($AvVideo));

			return $AvVideo;
		}
		return json_decode($AvVideo, true);

	}
	public static function getActor($id) {

		$key = 'AvActor-'.$id;
		$AvActor = Redis::get($key);
		if (is_null($AvActor)) {
			$AvActor = \App\AvActor::where('id',$id)->first();
			$AvActor = $AvActor ? $AvActor->toArray() : array();
			Redis::setex($key, 60, json_encode($AvActor));
			return $AvActor;
		}
		return json_decode($AvActor, true);

	}

	public static function getRedis($key) {
		return Redis::get($key);
	}

	public static function previewToken($_fileName) {
		//$ipLimitation = true; //加入ip認證
		//$secret = "Av#Baby";             // Same as AuthTokenSecret
		$protectedPath = "/previews/";        // Same as AuthTokenPrefix
		//$hexTime = dechex(time());             // Time in Hexadecimal      
		//$hexTime = time() + (3600*3);             // 3 小時後過期
		//$hexTime = time() + (3600);             // 3 小時後過期
	//	$fileName = $protectedPath.$_fileName;    // The file to access
	//	$ip = $_SERVER['REMOTE_ADDR'];

	//	$token = md5($secret . $fileName . $hexTime . $ip); 
//		$url =  $fileName . "?token={$token}&expire={$hexTime}";

		//return 'http://video.twdio.com'.$url;
		//return  'http://video.twdio.com:8081'.$url;
		//return  'http://125.227.59.96'.$protectedPath.$_fileName;
		return  '//avi-jibamao.cdn.hinet.net'.$protectedPath.$_fileName;

		//return  'http://video-jibamao.cdn.hinet.net'.$protectedPath.$_fileName;

	}

}
