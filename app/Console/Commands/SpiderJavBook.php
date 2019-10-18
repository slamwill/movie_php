<?php

namespace App\Console\Commands;

use JoggApp\GoogleTranslate\GoogleTranslateClient;
use Illuminate\Console\Command;
use Intervention\Image\ImageManagerStatic as Image;
//ini_set('memory_limit', '-1');


class SpiderJavBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:javbook {avkey*} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬javbook.com';

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
		$type = isset($array[1]) ? intval($array[1]) : 0; 
		$source = isset($array[2]) ? $array[2] : ''; 

		$this->spider($avkey,$type, $source);
		$this->info("Avkey:[{$avkey}]抓取完成");
    }
	public function curl($avkey,$type){


		if ($type == 1) {
			$command = "curl 'https://javbooks.com/serch_censored/{$avkey}/serialall_1.htm'";
		}
		else {
			$command = "curl 'https://javbooks.com/serch_uncensored/{$avkey}/serialall_1.htm'";
		}
		//echo $command;
		return shell_exec($command);
	
	}
	public function spider($avkey = '', $type = '', $source = ''){

		if ($source) {
			$html = new \Htmldom($source);
		}
		else {

			$Htmldom = new \Htmldom();
			$str = $this->curl($avkey, $type);
			$str = str_replace("\n","",$str);
			$str = str_replace("\t","",$str);

			$html = $Htmldom->str_get_html($str);



			$spider=[];

			
			foreach ( $html->find('div[class=Po_u_topic]') as $item){

				$key = trim(explode('/',$item->find('div[class=Po_u_topic_Date_Serial] font',0)->plaintext)[0]);
				
				$spider[$key] = [
					'url' => $item->find('div[class=Po_u_topic_title] a',0)->href,
					'key' =>$key,

				];
				
			}


			$mapKey = str_replace('_','-',$avkey);

			if (!isset($spider[$mapKey]) && !isset($spider[$avkey])) {
				$this->info("Avkey:[".$avkey."] Javbook 找不到");
				abort(500, 'Something went wrong');
				exit;
			}
			if (isset($spider[$mapKey])) {
				$fetchUrl = $spider[$mapKey]['url'];
			}
			else {
				$fetchUrl = $spider[$avkey]['url'];			
			}
			$html = new \Htmldom($fetchUrl);
		}

		$video_type = 0;
		if ($type) $video_type = 1;

		$videos = [];

		$videos['title'] = $html->find('div[id=title] b',0)->plaintext;

		$actors=[];
		$tags=[];
		foreach ( $html->find('div[class=infobox]') as $item){

			if(strpos($item->innertext,'發行時間：') !== false){
				$videos['release_date'] = trim(explode('</b>',$item->innertext)[1]);
			}
			if(strpos($item->innertext,'影片類別：') !== false){
				foreach ( $item->find('a') as $row){
					$tags[] = trim($row->plaintext);
				}
				$videos['tags'] = $tags;
			}
			if(strpos($item->innertext,'女優：') !== false){
				foreach ( $item->find('a') as $row){
					$actors[] = [
						'avatar' => null,
						'name' => trim($row->plaintext),
					];	
				}

				if (!count($actors) && $type != 2) {
					$actors[] = [
						'avatar' => null,
						'name' => '素人',
					];
				}
						
				$videos['actors'] = $actors;
			}
			
		
		}

		if ($tags) {
			$text = implode('，',$tags);
			$GoogleTranslate = new GoogleTranslateClient(config('googletranslate'));
			$result = $GoogleTranslate->translate($text,'zh-CN');
			$tags = explode('，',$result['text']);
			$videos['tags'] = $tags;
			
		}


		$videos['cover'] = $html->find('div[class=info_cg] img',0)->src;
		$videos['avkey'] = $avkey;
		$videos['duration'] = 0;

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
