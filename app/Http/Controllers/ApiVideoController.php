<?php

namespace App\Http\Controllers;

use App\Banner;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\View;
use DB;

//use App\Http\Controller\Api\VideoController;


class ApiVideoController extends Controller
{

	public $pageNumber = 20;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

	//最新影片
	public function latest(Request $request){

		$key = 'hdLatest-nuxt-vue'.$request->page;
		$avkeys = Redis::get($key);

		if (is_null($avkeys)) {
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();

			$time = date('Y-m-d', strtotime('-1 day', strtotime( date("r") )));

			foreach( $avkeys["data"] as $key => $avkey ){
				$avkeys["data"][$key]["new_tag"] = 0;
				if( $avkey["updated_at"] > $time )
				{
					$avkeys["data"][$key]["new_tag"] = 1;
				}
			}

			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}

		return response()->json(json_decode($avkeys, true));
	}


	public function onceVideo(Request $request){

		//$video = \App\Classes\Common::getVideo($request->avkey);
		$key = 'AvVideos-'.$request->avkey;
		$AvVideo = Redis::get($key);

		if (is_null($AvVideo)) {

			$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$request->avkey)->first();
			$AvVideo = $AvVideo ? $AvVideo->toArray() : array();
			if ($AvVideo) {

				$AvVideo['actors_name'] = [];
				if (isset($AvVideo['actors'])) {
					$AvVideo['actors_name'] = \App\AvActor::whereIn('id',explode(',', $AvVideo['actors']))->get()->pluck('name','id')->toArray();
				}
				$AvVideo['tags_name'] = [];
				if (isset($AvVideo['tags'])) {
					$AvVideo['tags_name'] =	\App\AvTag::whereIn('id',explode(',', $AvVideo['tags']))->get()->pluck('name','id')->toArray();
				}

				if ($AvVideo['cover_index']) {
					$AvVideo['cover_index'] = '//avi-jibamao.cdn.hinet.net/previews/'.$AvVideo['avkey'].'/preview'.( $AvVideo['cover_index'] - 1).'b.png';
				}
				
				// 猜你喜歡
				$AvVideo['maybeYouLike'] = [];
				if (isset($AvVideo['maybeYouLike'])) {
					$AvVideoTag = \App\AvVideo::where('enable','on')->where('video_source', $AvVideo['video_source'])->where('is_free',$AvVideo['is_free'])->where('video_type', $AvVideo['video_type'])->orderBy('updated_at', 'desc')->where('avkey','!=',$AvVideo['avkey'])->inRandomOrder()->take(12)->pluck('avkey')->toArray();			
					if ($AvVideoTag) foreach ($AvVideoTag as $_avkey){
						$video = $this->getVideo($_avkey);
						if ($video) $MaybeYouLike[] = $this->getVideo($_avkey);
					}
					$AvVideo['maybeYouLike'] = $MaybeYouLike;
					$AvVideo['RightBoxVideos'] = $MaybeYouLike;
				}

				// 热门相关影片
				/*
				$AvVideo['RightBoxVideos'] = [];
				if (isset($AvVideo['RightBoxVideos'])) {
					
					$AvVideoTag = \App\AvVideo::where('enable','on')->where('video_source', $AvVideo['video_source'])->where('is_free',$AvVideo['is_free'])->where('video_type', $AvVideo['video_type'])->orderBy('updated_at', 'desc')->where('avkey','!=',$AvVideo['avkey'])->inRandomOrder()->take(12)->pluck('avkey')->toArray();			
					if ($AvVideoTag) foreach ($AvVideoTag as $_avkey){
						$video = $this->getVideo($_avkey);
						if ($video) $RightBoxVideos[] = $this->getVideo($_avkey);
					}
					
					$AvVideo['RightBoxVideos'] = $MaybeYouLike;
				}
				*/


			}

			//var_dump($AvVideo);
			//print_r($AvVideo);



			$AvVideo = json_encode($AvVideo);
			Redis::setex($key, 5, $AvVideo);

		}
		return response()->json(json_decode($AvVideo, true));

	}

	private function getVideo($avkey) {
		//print_r("aaaaaaaaaaaa");
		return \App\Classes\Common::getVideo($avkey);
	}

	//手動分頁
	private function paginate(&$items,$perPage) {
		return \App\Classes\Common::paginate($items,$perPage);
	}


	//高清有码
	public function hdCensored(Request $request){
		$key = 'hdCensored-nuxt-vue'.$request->page;
		$avkeys = Redis::get($key);
		if (is_null($avkeys)) {
			//$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',0)->where('video_type',1)->orderBy('updated_at','desc')->paginate(20)->toArray();
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->where('video_source',0)
				->where('video_type',1)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();

			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}		
		return response()->json(json_decode($avkeys, true));
	}

	//高清无码
	public function hdUncensored(Request $request){
		$key = 'hdUncensored-nuxt-vue-'.$request->page;
		$avkeys = Redis::get($key);
		if (is_null($avkeys)) {
			//$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',0)->where('video_type',0)->orderBy('updated_at','desc')->paginate(20)->toArray();
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->where('video_source',0)
				->where('video_type',0)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();
			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}		
		return response()->json(json_decode($avkeys, true));
	}



	public function hdUnited(Request $request){
		$key = 'hdUnited-nuxt-vue-'.$request->page;
		$avkeys = Redis::get($key);
		if (is_null($avkeys)) {
			//$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',1)->orderBy('updated_at','desc')->paginate(20)->toArray();
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->where('video_source',1)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();
			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}
		return response()->json(json_decode($avkeys, true));
	}

	//高清卡通
	public function hdCartoon(Request $request){
		$key = 'hdCartoon-nuxt-vue-'.$request->page;
		$avkeys = Redis::get($key);
		if (is_null($avkeys)) {
			$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',2)->orderBy('updated_at','desc')->paginate(20)->toArray();
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->where('video_source',2)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();
			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}		
		return response()->json(json_decode($avkeys, true));
	}
	//高清自拍
	public function hdSelf(Request $request){
		$key = 'hdSelf-nuxt-vue-'.$request->page;
		$avkeys = Redis::get($key);
		if (is_null($avkeys)) {
			$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',3)->orderBy('updated_at','desc')->paginate(20)->toArray();
			$avkeys = \App\AvVideo::leftJoin('av_actors', 'av_videos.actors', '=', 'av_actors.id')
				->select('av_videos.*' ,'av_actors.name')
				->where('enable', '=', 'on')
				->where('is_free', '=', 0)
				->where('video_source',3)
				->orderBy('updated_at','desc')
				->paginate(20)
				->toArray();
			$avkeys = json_encode($avkeys);
			Redis::setex($key, 5, $avkeys);
		}		
		return response()->json(json_decode($avkeys, true));
	}




	//搜尋
	public function search($keyword){

		$actors = \App\AvVideoActor::whereIn('actor_id', function ($query) use($keyword) {
			$query->select('id')
			->from(with(new \App\AvActor)->getTable())
			->where('name','like',"%{$keyword}%");
		})->pluck('video_id')->toArray();	

		//$actors = \App\AvActor::where('name','like',"%{$keyword}%")->pluck('video_id')->toArray();
		
		$tags = \App\AvVideoTag::whereIn('tag_id', function ($query) use($keyword) {
			$query->select('id')
			->from(with(new \App\AvTag)->getTable())
			->where('name','like',"%{$keyword}%");
		})->pluck('video_id')->toArray();
		
		//$tags = \App\AvTag::where('name','like',"%{$keyword}%")->pluck('video_id')->toArray();
		
		$videos = \App\AvVideo::where('enable','on')->where('title','like',"%{$keyword}%")->pluck('id')->toArray();
		$array = array_unique(array_merge($actors, $tags, $videos));

		$avkeys = \App\AvVideo::where('enable','on')->whereIn('id', $array)->orderBy('updated_at','desc')->pluck('avkey', 'id')->toArray();

		//$links = $this->paginate($avkeys, 20)->links('layouts/paginator');
		$links = $this->paginate($avkeys, 20);

		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return response()->json( ['AvVideos' => $AvVideos, 'links' => $links, 'keyword' => $keyword] );
		//return response()->json( ['AvVideos' => $AvVideos, 'links' => $links, 'keyword' => $keyword] );
	}


	//標籤
	public function tag($string) {
		$allVideoTags = \App\Classes\Common::getAllVideoTags();

		if(! isset($allVideoTags[$string]))
		{
			 return redirect()->route('home');
		}
		$id = $allVideoTags[$string];

		if(!$id) return redirect()->route('home');

		$avkeys = \App\AvVideo::where('enable','on')->whereIn('id', function ($query) use($id) {
			$query->select('video_id')
			->from(with(new \App\AvVideoTag)->getTable())
			->where('tag_id', $id);
		})->orderBy('updated_at','desc')->pluck('avkey', 'id')->toArray();

		//$links = $this->paginate($avkeys, 20)->links('layouts/paginator');
		$links = $this->paginate($avkeys, 20);


		//$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');

		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return response()->json( ['AvVideos' => $AvVideos, 'links' => $links, 'tag' => $string] );

	}







}
