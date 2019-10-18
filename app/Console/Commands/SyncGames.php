<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SyncGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncGames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步遊戲參數';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

	public function curl($url){
		return shell_exec("curl '{$url}'");
	}
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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

	
}
