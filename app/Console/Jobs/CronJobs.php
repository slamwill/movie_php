<?php

namespace App\Console\Jobs;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Facades\Log;


class CronJobs
{
    /**
     * 工作排程
     *
     * @return void
     */
    public function __construct()
    {
        //parent::__construct();
    }
	public function curl($url){
		return shell_exec("curl '{$url}'");
	}

	public function launchVideos(){
		$current = date('Y-m-d H:i:s', time());
		$AvVideo = \App\AvVideo::where('enable','off')->whereNotNull('auto_release_date')->where('auto_release_date', '<=', $current)->get();
		foreach($AvVideo as $value)
		{
			\App\AvVideo::find($value->id)->update(['enable' => 'on']);
		}
	}

	public function syncGames(){
		//遊戲列表
		$url = env('APP_URL').'/serviceAg/rest/applicationService/games/'.env('SUB_COMPANY');

		$result = json_decode($this->curl($url), true);

		$key = 'GamesList';
		$GamesList = Redis::get($key);
		if (is_null($GamesList) && $result) {
			Redis::setex($key, 86400, json_encode($result));
		}
		else {
			if ($result) {
				Redis::setex($key, 86400, json_encode($result));
			}
		}

		//最新公告
		$url = env('APP_URL').'/serviceAg/rest/userCentralService/queryNoticeInfos/'.env('SUB_COMPANY');
		$result = json_decode($this->curl($url), true);

		$key = 'WebNotices';
		$WebNotices = Redis::get($key);
		if (is_null($WebNotices) && $result) {
			Redis::setex($key, 86400, json_encode($result));
		}
		else {
			if ($result) {
				Redis::setex($key, 86400, json_encode($result));
			}
		}	
	
	}



	//抓取真人直播api存入redis
	public function syncLiveApi(){
	
		$response = file_get_contents('https://tom29.com/api/live.php?method=live');
		$response = substr($response,2,-2);
		$Aes = new \PhpAes\Aes('w5ou1xt6h78sm0sql1dj146gl5537800', 'CBC', '7vow63w1dol964c1');
		$json = $Aes->decrypt(base64_decode($response));

		if ($json) {
			$result = array();
			foreach (json_decode($json,true) as $rows){
				if(@getimagesize($rows['pic'])){
					$result[] = $rows;			
				}
			}
			$result = json_encode($result);
		}
		else {
			$result = $json;
		}

		//直播API
		$key = 'LiveVideos';
		$LiveVideos = Redis::get($key);
		
		if (is_null($LiveVideos) && $result) {
			Redis::setex($key, 86400, $result);
		}
		else {
			if ($result) {
				Redis::setex($key, 86400, $result);
			}
		}
	}



	
}
