<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
		'App\Console\Commands\SyncGames',
		'App\Console\Commands\SpiderVideos',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

		$schedule->command('bifu')->everyMinute()->description('每分鐘抓bifu支付訂單')->withoutOverlapping();

		$schedule->command('nnex')->everyMinute()->description('每分鐘抓nnex支付訂單')->withoutOverlapping();


		//$schedule->command('SyncGames')->everyMinute();
       // $schedule->call(function () {

		//	$CronJobs = new \App\Console\Jobs\CronJobs;
			//遊戲排程
		//	$CronJobs->syncGames();
		//})->everyMinute()->description('遊戲訊息與Json同步排程');
        
        
        $schedule->call(function () {

			$CronJobs = new \App\Console\Jobs\CronJobs;
			//影片自動上架排程
            $CronJobs->launchVideos();
            // Log::info(json_encode([123,456]));
        })->everyTenMinutes()->description('影片自動上架排程');


		
		//$schedule->call(function () {

			//$CronJobs = new \App\Console\Jobs\CronJobs;
			//抓取真人直播api存入redis
			//$CronJobs->syncLiveApi();
		//})->everyMinute();
		//})->everyFifteenMinutes()->description('暫無作用');

		//$schedule->call(function () {

			//$SyncDiyizy8 = new \App\Console\Jobs\SyncDiyizy8;
			//$SyncDiyizy8->sync();

		//	$Sync4hu = new \App\Console\Jobs\Sync4hu;
		//	$Sync4hu->sync();
			//清cache
		//	Redis::select(0);
		//	Redis::flushdb();

		//})->dailyAt('03:00')->description('4虎影片同步');

        // 將 Redis 中的 pageview 寫入 DB
        $schedule->call(function () {
                $StorePageview = new \App\Console\Jobs\StorePageview();
                $StorePageview->exec('uri');
                $StorePageview->exec('ip');
                $StorePageview->exec('ref');
        })->everyFiveMinutes()->description('每5分鐘寫入DB');

        $schedule->call(function () {
            $HomePageJobs = new \App\Console\Jobs\HomePageJobs();
            $HomePageJobs->bannerHomePageJobs();
        })->hourly()->description('首頁五宮格產生, 每一小時');  //every 12 hours
        // })->twiceDaily(1, 13)->description('首頁Banner自動調整, 每日01:00和13:00');  //every 12 hours
        // })->cron('0 */3 * * *')->description('首頁Banner自動調整');  //every 3 hours
        //$schedule->call(function () {
        //    $HomePageJobs = new \App\Console\Jobs\HomePageJobs();
        //    $HomePageJobs->tagHomePageJobs();
        //})->hourlyAt(23)->description('首頁Tag自動調整, 每一小時的23分');  //every 12 hours

		// })->twiceDaily(2, 14)->description('首頁Tag自動調整, 每日02:00和14:00');  //every 12 hours
        // })->cron('0 */3 * * *')->description('首頁Banner自動調整');  //every 3 hours

		// 每日點級數最高分類
		$schedule->call(function () {
		    $CountPageview = new \App\Console\Jobs\CountPageview();
            $CountPageview->countPageviewCategory();
        })->dailyAt('02:00')->description('每日02:00執行統計點級數最高分類數據');

        // 每日點級數最高影片
        $schedule->call(function () {
            $CountPageview = new \App\Console\Jobs\CountPageview();
            $CountPageview->countPageviewWatch();
        })->dailyAt('02:01')->description('每日02:01執行統計點級數最高影片數據');

        // 每日點級數最高Tag
        $schedule->call(function () {
            $CountPageview = new \App\Console\Jobs\CountPageview();
            $CountPageview->countPageviewTag();
        })->dailyAt('02:02')->description('每日02:02執行統計點級數最高標籤數據');

        // 月報表
        $schedule->call(function () {
            $CountPageview = new \App\Console\Jobs\CountPageview();
            $CountPageview->countPageviewDaily();
        })->dailyAt('02:03')->description('每日02:03執行統計點月報表數據');

		$schedule->call(function () {
            $CountPageview = new \App\Console\Jobs\CountPageview();
            $CountPageview->countPageviewFiveMinutes();
        })->everyFiveMinutes()->description('VideoViews每五分鐘寫到DB一次');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
