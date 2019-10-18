<?php

namespace App\Console\Jobs;

use App\AdminConfig;
use App\AvVideo;
use App\ReportTagMonth;

use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class HomePageJobs
{

    public function bannerHomePageJobs()
    {
        Redis::del('IndexVideosJson');

        //欧美
        $AvVideos = \App\AvVideo::where('enable','on')->where('video_source',1)->where('is_free',0)->orderBy('views', 'desc')->take(5)->get();

        foreach($AvVideos as $index => $AvVideo)
		{
            $IndexVideoArray[0][$index] = $AvVideo;
            $ConfigIndexVideoArray[0][$index] = $AvVideo->avkey;
        }

        //亞洲無碼
        $AvVideos = \App\AvVideo::where('enable','on')->where('video_source',0)->where('video_type',0)->where('is_free',0)->orderBy('views', 'desc')->take(5)->get();

		foreach($AvVideos as $index => $AvVideo)
		{
            $IndexVideoArray[1][$index] = $AvVideo;
            $ConfigIndexVideoArray[1][$index] = $AvVideo->avkey;
        }

        //亞洲有碼
        $AvVideos = \App\AvVideo::where('enable','on')->where('video_source',0)->where('video_type',1)->where('is_free',0)->orderBy('views', 'desc')->take(5)->get();
		foreach($AvVideos as $index => $AvVideo)
		{
            $IndexVideoArray[2][$index] = $AvVideo;
            $ConfigIndexVideoArray[2][$index] = $AvVideo->avkey;
        }


        $IndexVideoArray = json_encode($IndexVideoArray);
        $key = 'IndexVideosJson'; //首頁五宮格
        Redis::setex($key, 86400, $IndexVideoArray);

        $ConfigIndexVideoArray = json_encode($ConfigIndexVideoArray, JSON_UNESCAPED_UNICODE);

        AdminConfig::where('name', 'IndexVideosJson')->update(['value' => $ConfigIndexVideoArray]);
    }

    public function tagHomePageJobs()
    {
        Redis::del('Top6TagsJson');

        // $AvTags = \App\AvTag::orderBy('views', 'desc')->take(6)->get();

        $AvTags = DB::table('av_tags')
            ->select(DB::raw("*"))
            ->rightJoin(DB::raw("(SELECT sum(views) as views , `title` FROM `report_tag_month` GROUP BY `title` ORDER BY `views` desc limit 0,6) r"), 'av_tags.name', '=', 'r.title')
            ->orderBy('r.views', 'desc')
            ->take(6)
            ->get();

        // $AvTags = ReportTagMonth::groupBy('title')
        //     ->selectRaw('sum(views) as sum, title')
        //     ->orderBy('sum', 'desc')
        //     ->take(6)
        //     ->get();

		foreach($AvTags as $index => $AvTag)
		{
            $Top6TagsArray[$index] = $AvTag->id.'|'.$AvTag->name;
        }

        $Top6TagsArray = json_encode($Top6TagsArray, JSON_UNESCAPED_UNICODE);

        $key = 'Top6TagsJson'; //首頁五宮格
        Redis::setex($key, 86400, $Top6TagsArray);

        AdminConfig::where('name', 'tags_order')->update(['value' => $Top6TagsArray]);
    }
    
}