<?php

namespace App\Console\Commands;

use JoggApp\GoogleTranslate\GoogleTranslateClient;
use Illuminate\Console\Command;
use Intervention\Image\ImageManagerStatic as Image;

class SpiderVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:video {avkey*} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬AVKEY';

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
		//return shell_exec("curl '{$url}' -H 'DNT: 1' -H 'Accept-Encoding: gzip, deflate, br' -H 'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7,zh-CN;q=0.6,ja;q=0.5' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8' -H 'Referer: https://avos.pw/ja/star/n0l' -H 'Cookie: AD_enterTime=1512252497; __test; AD_clic_j_POPUNDER=2; AD_adst_j_POPUNDER=2; ppu_main_70829c38216e1c04616adb4672eda342=1; AD_exoc_j_POPUNDER=2; splash_i=false; AD_juic_j_POPUNDER=1; AD_exoc_j_L_728x90=4; AD_exoc_j_M_728x90=2; AD_wav_j_P_728x90=2; AD_juic_j_L_728x90=10; _ga=GA1.2.585333520.1512252496; _gid=GA1.2.1375831346.1512252496' -H 'Connection: keep-alive' -H 'If-Modified-Since: Sat, 02 Dec 2017 22:15:37 GMT' -H 'Cache-Control: max-age=0' --compressed");
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
		$spiderUrl = isset($array[1]) ? $array[1] : null; 
		if ($avkey && !$spiderUrl) {			
			$this->spiderJavbusVideo($avkey, $spiderUrl);
		}
		else {
		
			if (strpos($spiderUrl,'javbus')) {
				$this->spiderJavbusVideo($avkey, $spiderUrl);
			}
			else {
				$this->spiderVideo($avkey, $spiderUrl);		
			}
		}
		//
		$this->info("Avkey:[{$avkey}]抓取完成");
    }
	public function spiderJavbusVideo($avkey = '', $spiderUrl = ''){

		if (!$spiderUrl) {

			if (preg_match('/-v\d$/',$avkey)) {
				$tempAvkey = preg_replace("/-v\d$/",'',$avkey); 

				$html = new \Htmldom("https://www.javbus.com/{$tempAvkey}");
			}
			else {
				if (count(explode('-',$avkey)) >= 4) {

					//1By-Day部份資料有問題 ，特別拉出來判斷
					if (strlen(strpos($avkey,'1By-Day'))) {
						$url = "https://www.javbus.org/{$avkey}";
						$html = $this->curl($url);
						if (strlen(strpos($html, '404 Page Not Found!'))) {
							$avkeyTemp = str_replace('1By-Day','1ByDay', $avkey);
							$html = new \Htmldom("https://www.javbus.org/{$avkeyTemp}");
						
						}
						else {
							$html = new \Htmldom("https://www.javbus.org/{$avkey}");					
						}

					}
					else {

						$html = new \Htmldom("https://www.javbus.org/{$avkey}");

					}

				}
				else {
					$html = new \Htmldom("https://www.javbus.com/{$avkey}");				
				}
			}
		}
		else {
			$html = new \Htmldom($this->curl($spiderUrl));
		}
		if (!$html) {
			$this->info("Avkey:[".$avkey."] Javbus 找不到");
			exit;
		}
		$video_type = 0;
		$cover = $html->find('a[class=bigImage]',0)->href;
		//$info = $html->find('div[class=info] p', 0);
		$videos = [];


		$typeName = $html->find('ul[class=navbar-nav] li[class=active]',0)->plaintext;
		if (trim($typeName) == '無碼') {
			$video_type = 0;
		}
		elseif(trim($typeName) == '有碼') {
			$video_type = 1;
		
		}

		$videos['title'] = $html->find('div[class=container] h3',0)->plaintext;

		$videos['cover'] = $html->find('a[class=bigImage]',0)->href;
		$videos['tags'] = [];
		foreach ( $html->find('div[class=info] p') as $item){
			if (strpos($item->innertext,'識別碼')) {
				$videos['avkey'] = trim($item->find('span',1)->plaintext);
			}

			if (strpos($item->innertext,'發行日期')) {
				$videos['release_date'] = preg_replace('/[^0-9\-]/','',$item->plaintext) ;
			}
			if (strpos($item->innertext,'長度')) {
				$videos['duration'] = preg_replace('/[^\d]/','',$item->plaintext) ;
			}
			if (strpos($item->innertext,'genre') && strpos($item->innertext,'onmouseover') === false) {
				$tags = [];
				foreach($item->find('span[class=genre]') as $tag) {
					$tags[] = trim($tag->plaintext);
				}
				$videos['tags'] = $tags;
			}
			
		}


		if ($videos['tags']) {
			$text = implode('，',$videos['tags']);
			$GoogleTranslate = new GoogleTranslateClient(config('googletranslate'));
			$result = $GoogleTranslate->translate($text,'zh-CN');
			$tags = explode('，',$result['text']);
			$videos['tags'] = $tags;
			
		}

		$videos['title'] = trim(str_replace($videos['avkey'],'',$videos['title']));
		if ($videos['avkey'] != $avkey) {
			$videos['avkey'] = $avkey;
		}
		$actors=[];
		foreach ($html->find('div[class=info] ul li') as $actor) {
			$src = trim($actor->find('img',0)->src);
			//echo $src;
			$name = trim($actor->find('img',0)->title);
			$actors[] = [
				'avatar' => (strpos($src, 'nowprinting') === false) ? $src : null,
				'name' => $name,
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
	public function spiderVideo($avkey = '', $spiderUrl = ''){
	
		//$avkey = 'MCBD-17';

		if ($spiderUrl) {
			$html = new \Htmldom($this->curl($spiderUrl));
		}
		else {
			$html = new \Htmldom($this->curl("https://javfee.com/ja/search/{$avkey}"));

		}
		//$html = file_get_contents('https://avso.club/ja/search/'.$avkey);

		$exists = (strpos($html->plaintext,'検索条件') === false) ? true : false;
		$video_type = 0; //無碼

		if (!$exists) {

			$content = $this->curl("https://javtag.com/ja/search/{$avkey}");
			$html = new \Htmldom($content);	
			//$html = new \Htmldom('https://avos.pw/ja/search/'.$avkey);	

			$exists = (strpos($html->plaintext,'検索条件') === false) ? true : false;

			$video_type = 1; //有碼
			if (!$exists) {
				$this->info("Avkey:[".$avkey."] avso, avmo 找不到");
				exit;
			}
		}


		foreach ($html->find('div[id=waterfall] div[class=item]') as $item){
			$date = $item->find('date');

			if (!count($date)) continue;
			if (!$spiderUrl) {
				if ($date[0]->innertext != $avkey) continue;
			}
			$AvVideo = \App\AvVideo::where('avkey',$avkey)->first();
			if (!$AvVideo) $AvVideo = new \App\AvVideo;

			$url = $item->find('a[class=movie-box]',0)->href;

			$content = new \Htmldom(!$video_type ? $url : $this->curl($url));

			$cover = $content->find('a[class=bigImage]',0)->href;

			$info = $content->find('div[class=info]', 0);
			$duration = $info->find('p',2)->plaintext;

			$tags = [];
			foreach($info->find('span[class=genre]') as $tag) {
				$tags[] = trim($tag->plaintext);
			}
			$actors = [];
			foreach ($content->find('div[id=avatar-waterfall] a[class=avatar-box]') as $actor) {
				$src = trim($actor->find('img',0)->src);
				$actors[] = [
					'avatar' => (strpos($src, 'nowprinting') === false) ? $src : null,
					'name' => trim($actor->find('span',0)->plaintext)
				];			
			}

			$videos = [
				'url' => trim($item->children(0)->href),	
				'title' => trim($item->children(0)->children(0)->children(0)->title),
				//'avkey' => trim($date[0]->innertext),
				'avkey' => trim($avkey),
				'release_date' => trim($date[1]->innertext),
				'tags' => $tags,
				'cover' => trim($cover),
				'duration' => preg_replace('/[^\d]/','',$duration) ,
				'actors' => $actors,
			];

		}
		//if (!isset($AvVideo)) {
		//	$this->info("Avkey:[$avkey] avso, avmo 找不到");
		//	exit;
		//}

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

		//var_dump($videos['cover']);
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
