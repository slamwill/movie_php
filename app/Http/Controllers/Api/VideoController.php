<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class VideoController extends Controller
{


	//最新影片
	public function latest(){


		//$avkeys = \App\AvVideo::where('enable','on')->where('is_free',1)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$avkeys = \App\AvVideo::select('id','avkey')->where('enable','on')->where('is_free',0)->orderBy('updated_at','desc')->paginate(20);
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = \App\Classes\Common::getVideo($avkey->avkey);
		}

		$data = json_decode($avkeys->toJson(), true);
		$data['data'] = $AvVideos;
		return response()->json($data);

	}

	public function touchSwitchMenu(Request $request, $switch){

		\Session::put('SwitchMenu', $switch);
	
	}
	public function sync(Request $request){

		//\App\AvVideo::update(['has_video' => 1]);
		if (!$request->json) return response('no request data',500);
		
				
		\App\AvVideo::where('has_video', 1)->update(['has_video' => 0]);
		$json = json_decode($request->json, true);

		\App\AvVideo::whereIn('avkey',$json)->update(['has_video' => 1]);
		
		return response('sync success',200);
	}
	//消費5點下載影片
	public function consume($avkey){

		if (Auth::guest()) {
			return response()->json([ 'login' => route('login') ]);
		}
		$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$avkey)->first(['id','avkey']);
		if (!$AvVideo) {
			return response()->json(['error' => "番号错误!" ]);
		}

		$TransferLog = \App\TransferLog::where('user_id',Auth::User()->id)->where('type',3)->where('parent_id', $AvVideo->id)->where('created_at','>',date('Y-m-d H:i:s', strtotime("-2 day")))->first();
		if ($TransferLog) {
			return response()->json(['success' => true ]);
		}

		$downloadCoins = config('download_coins');

		$user = Auth::user();
		if ($user->coins - $downloadCoins < 0) {
			return response()->json(['error' => "您的点数不足!" ]);

		}

		$oldCoins = $user->coins;
		$user->increment('coins', -$downloadCoins);

		$json = [
			'coins' => ['from' => $oldCoins, 'to' => $user->coins],
			'expired' => date('Y-m-d H:i:s', strtotime("+2 day")),
		];
		$TransferLog = new \App\TransferLog;
		$TransferLog->order_no = uniqid(date('ymdhis'));
		$TransferLog->user_id = $user->id;
		$TransferLog->coins = -$downloadCoins;
		$TransferLog->user_coins = $user->coins;
		$TransferLog->type = 3;//config('av.translog')
		$TransferLog->parent_id = $AvVideo->id;
		$TransferLog->json = $json;
		$TransferLog->memo = $AvVideo->avkey;
		$TransferLog->save();
		return response()->json(['success' => true ]);
	
	}

	public function downloadConfirm($avkey){

		if (Auth::guest()) {
			return response()->json([ 'login' => route('login') ]);
		}
		$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$avkey)->first(['id']);
		if (!$AvVideo) {
			return response()->json(['error' => "番号错误!" ]);
		}


		$array = [
			'user_id' => Auth::User()->id,	
			'avkey' => $avkey,	
			'time' => time()
		];
		$encodeString = \houdunwang\crypt\Crypt::encrypt(json_encode($array),md5(env('DOWNLOAD_CRYPT')));


		$url = env('DOWNLOAD_API').'?q='.urlencode($encodeString);
		$TransferLog = \App\TransferLog::where('user_id',Auth::User()->id)->where('type',3)->where('parent_id', $AvVideo->id)->where('created_at','>',date('Y-m-d H:i:s', strtotime("-2 day")))->first();
		if ($TransferLog) {
			return response()->json([ 'success' => $url]);
		}
		else {
			if (Auth::User()->coins < intval(config('download_coins')) + 0) {
				return response()->json(['recharge' => '下载影片需要扣除'.config('download_coins').'点<br>您目前点数为'.Auth::User()->coins.'点，是否前往充值?', 'url' => route('user.recharge')]);		
			}

			
			return response()->json(['confirm' => '下载影片需要扣除'.config('download_coins').'点，将为期48小时时效内下载，是否确定下载影片?<br>(您目前点数为'.Auth::User()->coins.'点)' ]);		
		}


		return response()->json([ 'success' => $url]);
		//return response()->json([ 'success' => $this->MP4Token($avkey.'.mp4')]);
	}
	
	public function download($avkey){

		if (Auth::guest()) {
			return redirect()->route('login');
		}
		$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$avkey)->first(['id']);
		if (!$AvVideo) {
			return redirect()->back()->withErrors(['message' => '無此番號可提供購買']);
		}

		$TransLog = \App\TransLog::where('user_id',Auth::User()->id)->where('type',3)->where('parent_id', $AvVideo->id)->where('created_at','>',date('Y-m-d H:i:s', strtotime("-2 day")))->first();
		if (!$TransLog) {
			return redirect()->back()->withErrors(['message' => "{$avkey} 已過期或無購買記錄!"]);
		}

		//$avkey = '010914-518';
		$fullname = "/mnt/500g/videos/downloads/{$avkey}.mp4";

		header("X-Sendfile: {$fullname}");
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . basename($avkey) . '.mp4"');
	
	}
	
	//影片播放
	public function play(Request $request, $avkey) {




		//010914-518
		//$avkey = '010914-518';
		//$m3u8 = '010914-518/010914-518-SD.m3u8';
		$key = 'm3u8-'.$avkey;
		$string = Redis::get($key);

		//var_dump($avkey);exit;

		if (is_null($string)) {

			$url = $this->HLSToken("{$avkey}/{$avkey}.m3u8");

			try {
				if (!$string = @file_get_contents($url)) {
					throw new \Exception('Load Failed'); 
				}
			} catch (\Exception $e) {

				return abort(404);
				//return response('', 200);
			}

			Redis::setex($key, 86400, $string);
		}
		/*
		$Agent = new \Jenssegers\Agent\Agent;
		if ($Agent->isMobile()) {
			Redis::select(1);
			$session = Redis::get('laravel:'.Crypt::decryptString($request->token));
			if (!is_null($session)) {
				$session = unserialize(unserialize($session));
				$isLogin = count(preg_grep( '/^login_web.+/i', array_keys( $session ))) ? true : false;
			}
			else {
				$isLogin = false;
			}
		}
		else {*/
			$isLogin = Auth::check();
		//}
		
		//$xx = isMobile() ? 'aaa' : 'bbb';
		//file_put_contents('/tmp/sex.txt',$xx);

		$tsArray = explode(',',$string);
		//非會員
		//if (Auth::guest() || is_null(Auth::User()->expired) || time() > strtotime(Auth::User()->expired)) {
		//var_dump(Auth::check());
		//exit;
		/*
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}*/
		$ip = '';

		/*
		//使用 redis
		$is_free = 0;

		$key = 'FreeVideos';
		$FreeVideos = Redis::get($key);
		if (is_null($FreeVideos)) {
			$FreeVideos = array();
			$FreeVideos = \App\AvVideo::where('enable','on')->where('is_free','1')->get();
			$FreeVideos = json_encode($FreeVideos);
			Redis::setex($key, 3600, $FreeVideos);
		}
		$FreeVideos = json_decode($FreeVideos, true);

		foreach( $FreeVideos as $FreeVideo)
		{
			if( $FreeVideo['is_free'] == $avkey ) $is_free = 1;
		}
		*/


		//使用 cache
		$is_free = 0;

		/*
		$FreeVideos = Cache::remember('FreeVideos', 10, function() {
			return \App\AvVideo::where('enable','on')->where('is_free','1')->get()->pluck('id', 'avkey')->toArray();
		});

		if(!empty($FreeVideos[$avkey])) $is_free = 1;

		if ( ( !$isLogin || !auth::user()->expired || auth::user()->expired < date('Y-m-d H:i:s') ) && !$is_free   ) {
			$header = $tsArray[0];
			preg_match('/#EXT-X-TARGETDURATION:(\d+(\.\d+)?)/', $header, $match, PREG_OFFSET_CAPTURE);
			//$tsTotal = ceil(60 / intval($match[1][0]));
			//$tsArray = array_splice($tsArray, 20, $tsTotal+1);
			$tsTotal = ceil(20 / intval($match[1][0]));
			$tsArray = array_splice($tsArray, 10, $tsTotal+1);
			for($i=0; $i<count($tsArray); $i++){
				$res = explode("\n",($tsArray[$i]));
				if (strpos($res[1],'.ts') !== false) {
					$res[1] = $this->HLSToken($avkey.'/'.trim($res[1]), $ip);
					$tsArray[$i] = implode("\n",$res);
				}
			}
			$string = implode(",",$tsArray);
			$string = $header.','.$string;
			$string .= "\n#EXT-X-ENDLIST";
		}
		else {
			for($i=0; $i<count($tsArray); $i++){
				$res = explode("\n",($tsArray[$i]));
				if (strpos($res[1],'.ts') !== false) {
					$res[1] = $this->HLSToken($avkey.'/'.trim($res[1]), $ip);
					$tsArray[$i] = implode("\n",$res);
				}
			}
			$string = implode(",",$tsArray);
		}
		*/

		for($i=0; $i<count($tsArray); $i++){
			$res = explode("\n",($tsArray[$i]));
			if (strpos($res[1],'.ts') !== false) {
				$res[1] = $this->HLSToken($avkey.'/'.trim($res[1]), $ip);
				$tsArray[$i] = implode("\n",$res);
			}
		}
		$string = implode(",",$tsArray);




		$response = \response($string ,200);
		$response->header('Content-Type', 'application/x-mpegurl');
		return $response;

//		return response($string, 200);

		// We build the url 
		//echo '<a href="'.$url.'" target="_blank">'.$url.'</a>'; 
	}



	//影片播放
	public function AdminPlay($avkey) {
		
		$key = 'm3u8-'.$avkey;
		$string = Redis::get($key);

		if (is_null($string)) {

			$url = $this->HLSToken("{$avkey}/{$avkey}.m3u8");

			try {

				if (!$string = @file_get_contents($url)) {
					throw new \Exception('Load Failed'); 
				}

			} catch (\Exception $e) {


				return abort(404);
				//return response('', 200);
			}
			Redis::setex($key, 86400, $string);
		}
		/*
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}*/
		$ip = '';

		$tsArray = explode(',',$string);


		for($i=0; $i<count($tsArray); $i++){
			$res = explode("\n",($tsArray[$i]));
			if (strpos($res[1],'.ts') !== false) {
				$res[1] = $this->HLSToken($avkey.'/'.trim($res[1]), $ip);
				$tsArray[$i] = implode("\n",$res);
			}
		}
		$string = implode(",",$tsArray);

		return response($string, 200);

		// We build the url 
		//echo '<a href="'.$url.'" target="_blank">'.$url.'</a>'; 
	}


	//果核
	private function HLSToken($_fileName, $ip = '') {

		$uri = '/hls/'.$_fileName;
		$expires = time() + 10800;
		$secret = 'AvBody';
		/*
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}*/

		//$token = base64_encode(md5($uri.$secret.$ip.$expires, true));
		$token = base64_encode(md5($uri.$secret.$expires, true));
		#$token = base64_encode(md5($path.$secret.$expires, true));
		$token = strtr($token, '+/', '-_');
		$token = str_replace('=', '', $token);
		//$fileName = $path;    // The file to access

		$url =  $uri . "?token={$token}&expires={$expires}";

		return  'https://avi-jibamao.cdn.hinet.net'.$url;
		//return  'http://125.227.59.96'.$url;


	}
	
	private function HLSTokenOrigin($_fileName) {
		$ipLimitation = true; //加入ip認證
		$secret = "Av#Baby";             // Same as AuthTokenSecret
		$protectedPath = "/hls/";        // Same as AuthTokenPrefix
		//$hexTime = dechex(time());             // Time in Hexadecimal      
		//$hexTime = time() + (3600*3);             // 3 小時後過期
		$hexTime = time() + (3600 * 13);             // 3 小時後過期
		$fileName = $protectedPath.$_fileName;    // The file to access

		//$token = md5($secret . $fileName . $hexTime . $ip); 
		

		$url =  $fileName . "?token={$token}&expire={$hexTime}";
		//return 'http://video.twdio.com'.$url;

		//return  'http://video.twdio.com:8081'.$url;
		return  'http://125.227.59.96'.$url;
	}
	

	// 通知影片上架
	public function notify(Request $request, $avkey){


		$duration = intval($request->duration);
		$hours = floor($duration / 3600);
		$mins = floor($duration / 60 % 60);
		$secs = floor($duration % 60);
		$duration  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

		$video_source = substr($request->type,0,1);
		$video_type = substr($request->type,1,1);
		$AvVideo = \App\AvVideo::updateOrCreate(
			['avkey' => $avkey]
		);
		$AvVideo->has_video = 1;
		$AvVideo->duration = $duration;
		if (strlen($request->type)) {
			$AvVideo->video_source = intval($video_source);
			$AvVideo->video_type = intval($video_type);
		}
		$AvVideo->touch();
		$AvVideo->save();
		if (strtolower(substr($avkey,0,4)) == 'avid') {
			\Artisan::call('spider:jav', ['avkey' => [$avkey, $video_source]]);
		}
		else {
			\Artisan::call('spider:video', ['avkey' => [$avkey]]);
		}
		return response('ok', 200);
		exit;

	}
	

	public function TempMonkey(Request $request, $avkey) {

		$AvVideo = \App\AvVideo::where('avkey',$avkey)->first();
		$exists = 0;
		$has_video = 0;
		if ($AvVideo) {
			$exists = 1;
			$has_video = $AvVideo->has_video;
		}

		$data=['exists'=>$exists, 'has_video' => $has_video];
		$encode = json_encode($data);		
		//jsonp 方法 
		$jsonp = "{$request->callback}($encode)"; 

		return response($jsonp, 200);
	}

	public function iagree() {
		\Session::put('iagree', true);
		return response('', 200);
	}


	public function downloadVerification(Request $request){
		$data = ['status' => 0];
		$q = $request->get('q');
		$decrypted = \houdunwang\crypt\Crypt::decrypt($q, md5(env('DOWNLOAD_CRYPT')));
		if (!$decrypted) {
			return response()->json($data);
		}
		$json = json_decode($decrypted,true);

		$AvVideo = \App\AvVideo::where('enable','on')->where('avkey',$json['avkey'])->first(['id']);

		$TransferLog = \App\TransferLog::where('user_id',$json['user_id'])->where('type',3)->where('parent_id', $AvVideo->id)->where('created_at','>',date('Y-m-d H:i:s', strtotime("-2 day")))->first();
		if (!$TransferLog) {
			return response()->json($data);		
		}
		$data['status'] = 1;
		return response()->json($data);
		//return response()->json($data);
	}
}
