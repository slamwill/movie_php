<?php

namespace App\Console\Commands;

use JoggApp\GoogleTranslate\GoogleTranslateClient;
use Illuminate\Console\Command;
use Intervention\Image\ImageManagerStatic as Image;
//ini_set('memory_limit', '-1');


class SpiderJAV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:jav {avkey*} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬啪啪啪研習所資料';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

		//$array = explode('｜', $this->argument('avkey'));
		$array = $this->argument('avkey');
		$avkey = $array[0]; 
		$source = isset($array[1]) ? intval($array[1]) : 3; 
		$this->spiderJav($avkey, $source);
		$this->info("Avkey:[{$avkey}]抓取完成");
    }
	public function curl($avkey, $source){


		if ($source == 2) {
			//卡通
			$command = "curl 'https://hcg.jav101.com/play/animate/{$avkey}' -H 'authority: hcg.jav101.com' -H 'pragma: no-cache' -H 'cache-control: no-cache' -H 'upgrade-insecure-requests: 1' -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36' -H 'sec-fetch-mode: navigate' -H 'sec-fetch-user: ?1' -H 'dnt: 1' -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3' -H 'sec-fetch-site: none' -H 'referer: https://hcg.jav101.com/' -H 'accept-encoding: gzip, deflate, br' -H 'accept-language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' -H 'cookie: locale=tw; _ga=GA1.2.1862367962.1566046290; _gid=GA1.2.371258758.1566046290; enterLimit=true; CloudFront-Key-Pair-Id=APKAJBW3QQCETPXK5WRQ; _gat_UA-51244524-18=1; XSRF-TOKEN=eyJpdiI6IktsU0lubVJoQmo4UXZXR0dPaWJqbVE9PSIsInZhbHVlIjoiSXA1K1dxNzBvK1VkYjdxV0MyekE2Z2ZoQitiOVJuQmpXdGJBMXpvMWxVelZTcDU3NXhvY1FZQjBMZzJVV3JrcyIsIm1hYyI6IjI1NWM3YTYyMjEyNTI3YzZmZWM0ODVmMDQ2YTAzYTc2MzU1MzZhMDczOWZhNjhlODQxNWM5ZTU0ODkxMzNkYmUifQ%3D%3D; jav101_sessions=eyJpdiI6IjFYN2J6akE2YTZYYUk1Y2ZUUTYzRVE9PSIsInZhbHVlIjoia3JEVGMrT1VHR1ZFM0U3VDhmdFF5VGIyU1NhSU5RK1RkWmUzb0Q0SjFlK2YwQ29rcmVMNE42MWpXaWFNZHJVTiIsIm1hYyI6IjI0MGI5MTk3OWFlMjViODk2ZDE2MjAyMDMzNmIyYTY4ZmI5NzYwODU4M2FmY2MzYjUzMzdlNDgwMGIwMDhlMWQifQ%3D%3D; CloudFront-Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9zaHQuamF2MTAxLmNvbS8qL2ludHJvLyoiLCJDb25kaXRpb24iOnsiRGF0ZUxlc3NUaGFuIjp7IkFXUzpFcG9jaFRpbWUiOjE1NjYwNzExNDB9fX1dfQ__; CloudFront-Signature=jPG7AFpmCNgOw0iNr6gQ8cfal5h6n143HG-oY4r-4nWEeh40BNtcqGIFK52X3XqVOZ~Pl7ADMrAYX0zXr3G3wvLscULGbSxvHPR8-SQMC-YoSPmwuevLdhOC-Ff9EEzxS87sHvSXrcpFRt8GxlYY0ZPDi9QDql2wsze1kEPbwTtPsO81ixebL1LlcuIyXewjOnwKJY-b70CgSDoP0odwbEsESNXtNZW1vCwZh3pVp7k-3cQGl4ZOgrdwUgZnkRETTAOSZgbqv1iqtFOkrQjGUbTJMgUiN63~u0V4h2tAdcc5KntmKz00NbVoYzixQMuZujGzNUHVzJgHinlSWIRM5w__' --compressed";
		}
		if ($source == 0 || $source == 1) {
			//長片
			$command = "curl 'https://jav101.com/play/video/{$avkey}' -H 'authority: jav101.com' -H 'pragma: no-cache' -H 'cache-control: no-cache' -H 'upgrade-insecure-requests: 1' -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36' -H 'sec-fetch-mode: navigate' -H 'sec-fetch-user: ?1' -H 'dnt: 1' -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3' -H 'sec-fetch-site: none' -H 'accept-encoding: gzip, deflate, br' -H 'accept-language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' -H 'cookie: locale=tw; _ga=GA1.2.1862367962.1566046290; _gid=GA1.2.371258758.1566046290; enterLimit=true; CloudFront-Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9zbGEuamF2MTAxLmNvbS8qL2ludHJvLyoiLCJDb25kaXRpb24iOnsiRGF0ZUxlc3NUaGFuIjp7IkFXUzpFcG9jaFRpbWUiOjE1NjYwNzEwODB9fX1dfQ__; CloudFront-Signature=K4dqrDooswg2sUOOHOjA7PH6UVG1xp98EHQ5xhKdr4lJNaveMDyJu6Y3Ku8lQckRyGwgULW-2ucuxYX8HU6WsDtWuFuH~-SVgFjwlWZkY7hzZ~SB4C1vRu4tQwG4vELUVvFTakFdNsSFUuvBWqVu04o0YIQZzuYnvc9sXTids4rhFnCpTW~eLhpfwp2nTbe1wdOUok6O4xUNgQg20GeQZXJfHw~URVOC5HHdlkYKVF6K-YukE7j5nn4JIH4JM05hyfM0zVAvVGABZzVItC-VY6DdQcEJF1sPUuIY0Vx0OD3U2xp9IF7d2slB3G-JOfPb7Evg9BKICTKzMIBV~ky9DQ__; CloudFront-Key-Pair-Id=APKAJBW3QQCETPXK5WRQ; _gat_UA-51244524-6=1; _gat_UA-51244524-1=1; XSRF-TOKEN=eyJpdiI6Ilk1YmRtSDNWXC9zUTQ5Q1dnaDV0WFR3PT0iLCJ2YWx1ZSI6IjN5K2FUaVwvVGczSGV3d21sTmxWSlRQbHl3TXpMTlViTzVFNTVpRjczd21EYlFhUDh1cEFNR2hYUVhqYnFVVERrIiwibWFjIjoiMmNmZWU0MDYzZjM4YjY5ZWYwNzY4NjdkOTFkYmFiMzUzMjE2N2Q5NmMwYjcwYzIzMjRlMWU2MmQ4Nzk1YmMzZCJ9; jav101_sessions=eyJpdiI6ImdoeU54VlRBb3NlSzY2bXJnMWdWNEE9PSIsInZhbHVlIjoiakJhazlNR0JYdGpRUHVKaXA2RStjbmtFUEN4bEt5ZnJOaklSeStyR3JMTnRYYzE1c3RGMzhsd0EzamVzVGdpUSIsIm1hYyI6ImU3MTliY2I1YmEwZTkwMjQ5NTMxNTdjMDFiMTliODEyM2YyNmFiNjAwMmQ3NzI4ZWIzNDE1NjdkMzZhMzk3ODUifQ%3D%3D' --compressed";
		}
		if ($source == 3) {
			//短片
			$command = "curl 'https://v.jav101.com/play/{$avkey}' -H 'authority: v.jav101.com' -H 'pragma: no-cache' -H 'cache-control: no-cache' -H 'upgrade-insecure-requests: 1' -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36' -H 'sec-fetch-mode: navigate' -H 'sec-fetch-user: ?1' -H 'dnt: 1' -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3' -H 'sec-fetch-site: none' -H 'accept-encoding: gzip, deflate, br' -H 'accept-language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' -H 'cookie: XSRF-TOKEN=eyJpdiI6IlwvaGhDUEgyVkJhV2swTkoxcFp1b2JRPT0iLCJ2YWx1ZSI6InpvVG5wdDZrcmEyZXRrazRpWnUwYTFNVUtWUUd6d0ZFbkE4K2xnUVdQaTlGcmFmSzFnczVYVXJhc3A2ajNoYWIiLCJtYWMiOiJkZGQ0ZDVlMjFmZTQzNTc3NTdiZDlhMTdhMDRkZjFiYzIwMzhkZGM2NjE5NGI3OTIyZjE2MGY4NThlMjM2MzFiIn0%3D; jav101_sessions=eyJpdiI6IjVBcG1MVVUxMThhSnVLdVhKZFdcLzhBPT0iLCJ2YWx1ZSI6ImdTamUwZnBDRjJDYTZxUE14XC9PdWs3MStGdmVENzQ3NGpJM29ydnNYcDB3bzhhMWJBbGF0NWNjQmM3NkNNZE9ZIiwibWFjIjoiZmRiMjcyNTY4YjM4MmRlMjk4MGUwNjY5NGViMjc4NWZmMDAxOGM5ZmM2NmU0Y2FiMDQxODQ2MzRjMzhkY2Y0OSJ9; locale=tw; _ga=GA1.2.1862367962.1566046290; _gid=GA1.2.371258758.1566046290; _gat_UA-51244524-1=1; _gat_UA-51244524-7=1; _gat_UA-51244524-4=1; _gat_UA-89665360-5=1; enterLimit=true' --compressed";
		}
		
		return shell_exec($command);
	
	}
	public function spiderJav($avkey = '', $source = ''){

		$Htmldom = new \Htmldom();
		$str = $this->curl($avkey, $source);
		$str = str_replace("\n","",$str);
		$str = str_replace("\t","",$str);

		$html = $Htmldom->str_get_html($str);


//		if (!$html) {
//			$this->info("Avkey:[".$avkey."] Javbus 找不到");
//			exit;
//		}

		$video_type = 0;
		$videos = [];
/*
		$typeName = $html->find('ul[class=navbar-nav] li[class=active]',0)->plaintext;
		if (trim($typeName) == '無碼') {
			$video_type = 0;
		}
		elseif(trim($typeName) == '有碼') {
			$video_type = 1;
		
		}
*/
		$videos['title'] = $html->find('span[class=title]',0)->plaintext;


		$videos['cover'] = $html->find('meta[property=og:image]',0)->content;
		$videos['avkey'] = $avkey;
		$videos['release_date'] = $html->find('meta[property=video:release_date]',0)->content;
		//$videos['duration'] = '';
		foreach ( $html->find('span[class=tags] a') as $item){
			$tags[] = trim($item->plaintext);
		}
		$videos['tags'] = $tags;

		$GoogleTranslate = new GoogleTranslateClient(config('googletranslate'));
		$result = $GoogleTranslate->translate($videos['title'],'zh-CN');
		$videos['title'] = $result['text'];

		if ($tags) {
			$text = implode('，',$tags);
			$result = $GoogleTranslate->translate($text,'zh-CN');
			$tags = explode('，',$result['text']);
			$videos['tags'] = $tags;
			
		}

		
		$actors=[];
		foreach ( $html->find('span[class=actor_list] a') as $item){
			$actors[] = [
				'avatar' => null,
				'name' => trim($item->plaintext),
			];	
		}

		if (!count($actors) && $source != 2) {
			$actors[] = [
				'avatar' => null,
				'name' => '素人',
			];
		}
		
		$videos['actors'] = $actors;


		//封面
		//var_dump($videos['cover']);
		if ($this->get_http_response_code($videos['cover']) == "200") {
			try {
				$cover_path = public_path('uploads/covers/' . md5($videos['avkey']).'.jpg');
				if (file_exists($cover_path)) unlink($cover_path);
				Image::make($videos['cover'])->save($cover_path);
			//} catch (\Intervention\Image\Exception\ImageNotWritableException $e) {
			} catch (\Intervention\Image\Exception\NotReadableException $e) {
				$videos['cover'] = null;
			}
		}
		else {
			$videos['cover'] = null;
		}

		$VideoTags = [];
		foreach ($videos['tags'] as $tag){
			$AvTag = \App\AvTag::where('name', $tag)->first();
			if (!$AvTag) {
				$AvTag = new \App\AvTag;
				$AvTag->name = $tag;
				$AvTag->save();
			}
			$VideoTags[] = $AvTag->id;

		}



		//actors
		$VideoActors = [];
		foreach ($videos['actors'] as $actor){

			$AvActor = \App\AvActor::where('name', $actor['name'])->first();
			if (!$AvActor) $AvActor = new \App\AvActor;
			$AvActor->name = $actor['name'];
			$AvActor->origin_image = $actor['avatar'];
			$AvActor->save();

			if (!isset($AvActor->image)) {
				if ($actor['avatar']) {
					try {
						$actor_path = public_path('uploads/actors/' . md5($AvActor->id).'.jpg');
						if (file_exists($actor_path)) unlink($actor_path);
						Image::make($actor['avatar'])->save($actor_path);
						$AvActor->image = md5($AvActor->id).'.jpg';

						//Image::make($actor['avatar'])->save(public_path('uploads/actors/' . md5($actor['avatar']).'.jpg'));
					} catch (\Intervention\Image\Exception\NotReadableException $e) {
						$AvActor->image = null;
					}
					$AvActor->save();
				}
			}
			
			$VideoActors[] = $AvActor->id;

		}

		$AvVideo = \App\AvVideo::where('avkey',$avkey)->first();
		if (!$AvVideo) $AvVideo = new \App\AvVideo;

		$AvVideo->avkey = $videos['avkey'];
		$AvVideo->title = $videos['title'];
		$AvVideo->release_date = $videos['release_date'];
		if (!$AvVideo->duration) {
			$duration = intval($videos['duration'])*60;
			$hours = floor($duration / 3600);
			$mins = floor($duration / 60 % 60);
			$secs = floor($duration % 60);
			$duration  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
			$AvVideo->duration = $duration;		
		}

		$AvVideo->cover = $videos['cover'] ? md5($videos['avkey']).'.jpg' : null;
		$AvVideo->origin_cover = $videos['cover'];

		$AvVideo->actors = $VideoActors;
		$AvVideo->tags = $VideoTags;
		$AvVideo->video_type = $video_type;
		if (!$AvVideo->enable) $AvVideo->enable = 'off';
		//$AvVideo->enable = 'off';

		$AvVideo->save();


		\App\AvVideoTag::where('video_id',$AvVideo->id)->delete();
		foreach ($VideoTags as $tag){
			$AvVideoTag = new \App\AvVideoTag;
			$AvVideoTag->video_id = $AvVideo->id;
			$AvVideoTag->tag_id = $tag;
			$AvVideoTag->save();
		}

		\App\AvVideoActor::where('video_id',$AvVideo->id)->delete();
		foreach ($VideoActors as $actor){
			$AvVideoActor = new \App\AvVideoActor;
			$AvVideoActor->video_id = $AvVideo->id;
			$AvVideoActor->actor_id = $actor;
			$AvVideoActor->save();
		}

		if ($AvVideo->cover) {

			try {
				$cover_path = public_path('uploads/covers/'.$AvVideo->cover);
				$info = getimagesize($cover_path);
				$width = round($info[1] / 10 * 7.05);
				$height = $info[1];
				$x = round($info[0] - $width);
				$y = 0;

				$thumbnail =  md5($AvVideo->avkey).'.jpg';

				Image::make($cover_path)->crop($width,$height,$x,$y)->save(public_path('uploads/thumbnails/'.$thumbnail));

				$AvVideo->thumbnail = $thumbnail;

				$AvVideo->update();

			} catch (\Intervention\Image\Exception\NotReadableException $e) {

			}
		}
			


	}
	
	public function get_http_response_code($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

	
}
