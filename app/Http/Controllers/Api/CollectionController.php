<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;


class CollectionController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
	}

	
	public function watchs(Request $request){

		if (!Auth::check()) {
			return response()->json(['status' => -1,'msg'=>'請先登入', 'url'=> route('login')]);
		}

		$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
		if (!$AvVideo) 
			return response()->json(['status' => 0,'msg'=>'沒有該影片資料']);

		$AvUserWatch = \App\AvUserWatch::where(['user_id' => Auth::user()->id ,'video_id' => $AvVideo->id])->first();

		if (!$AvUserWatch) {
			$AvUserWatch = new \App\AvUserWatch;
			$AvUserWatch->user_id = Auth::user()->id;
			$AvUserWatch->video_id = $AvVideo->id;
			$AvUserWatch->views = 1;
			$AvUserWatch->save();
		}
		else {
			$AvUserWatch->increment('views' , 1);
			//$AvUserWatch->touch();
		}
		return response()->json(['status' => 1,'views' => $AvUserWatch->views]);


	}

	
	public function actors(Request $request){

		if (!Auth::check()) {
			return response()->json(['status' => -1,'msg'=>'請先登入', 'url'=> route('login')]);
		}

		$AvActor = \App\AvActor::where('name',$request->actor)->first();
		if (!$AvActor) 
			return response()->json(['status' => 0,'msg'=>'沒有該女優資料']);

		

		$AvUserActor = \App\AvUserActor::where(['user_id' => Auth::user()->id ,'actor_id' => $AvActor->id])->first();
		if ($AvUserActor) {
			\App\AvUserActor::where('id',$AvUserActor->id)->delete();
			return response()->json(['status' => 2,'msg'=>'已移除']);
		}
		else {
			$AvUserActor = new \App\AvUserActor;
			$AvUserActor->user_id =  Auth::user()->id;
			$AvUserActor->actor_id =  $AvActor->id;
			$AvUserActor->save();
			return response()->json(['status' => 1,'msg'=>'已收藏']);
		}


	}

	public function videos(Request $request){
		if (!Auth::check()) {
			return response()->json(['status' => -1,'msg'=>'請先登入', 'url'=> route('login')]);
		}

		$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();

		if (!$AvVideo) {
			return response()->json(['status' => 0,'msg'=>'沒有該影片資料']);
		}

		$AvUserVideo = \App\AvUserVideo::where(['user_id' => Auth::user()->id ,'video_id' => $AvVideo->id])->first();
		$key = 'Favorite-'.Auth::User()->id;

		if ($AvUserVideo) {
			if($request->action == "add")	// 小螢幕預覽 點擊收藏 使用者可能會連續一值點擊收藏 因此加此判斷
			{
				//確認redis中是否已經有key值(有保存時間限制) 如果沒有就把他加回來
				Redis::del($key);
				$boolMyFavoriteVideo = \App\Classes\Common::isMyFavoriteVideos($AvVideo);
				return response()->json(['status' => 1,'msg'=>'已收藏']);
			}
			else{	// 使用者想移除該觀看中且已收藏的影片
				//移除redis中的key值
				Redis::del($key);				
				//移除DB中的影片的資料
				\App\AvUserVideo::where('id',$AvUserVideo->id)->delete();
				return response()->json(['status' => 2,'msg'=>'已移除']);
			}
		}
		else {
			if($request->action == "delete")	// 小螢幕預覽 點擊移除 使用者可能會連續一值點擊移除 因此加此判斷
			{
				Redis::del($key);
				return response()->json(['status' => 2,'msg'=>'已移除']);
			}
			else{	// 使用者想收藏觀看中的影片
				$AvUserVideo = new \App\AvUserVideo;
				$AvUserVideo->user_id =  Auth::user()->id;
				$AvUserVideo->video_id =  $AvVideo->id;
				$AvUserVideo->save();
				Redis::del($key);
				$boolMyFavoriteVideo = \App\Classes\Common::isMyFavoriteVideos($AvVideo);
				return response()->json(['status' => 1,'msg'=>'已收藏']);
			}
		}
	}
}
