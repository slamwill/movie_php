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


//use App\Http\Controller\Api\VideoController;


class VideoController extends Controller
{

	public $pageNumber = 20;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
	   // $this->middleware('auth');
	   View::share('tag', '');
    }

	//標籤
	public function tag($string) {
		// $time1 = microtime(true);
		$allVideoTags = \App\Classes\Common::getAllVideoTags();
		// $time2 = microtime(true);
		// echo $time2 - $time1;
		// exit;
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

		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}
		
		//$tagArray = $this->getTags();
		View::share('tag', $string);

		return view('tag',['AvVideos' => $AvVideos, 'links' => $links]);
	}

	//女優
	public function actor($string){
		$allActorTags = \App\Classes\Common::getAllActorTags();

		if(! isset($allActorTags[$string]))
		{
			 return redirect()->route('home');
		}
		$id = $allActorTags[$string];
		if(!$id) return redirect()->route('home');
		
		//23|白咲奈々子
		$avkeys = \App\AvVideo::where('enable','on')->whereIn('id', function ($query) use($id) {
			$query->select('video_id')
			->from(with(new \App\AvVideoActor)->getTable())
			->where('actor_id', $id);
		})->pluck('avkey', 'id')->toArray();

		//Array ( [4] => FREE-2378 [6] => FREE-2380 [59] => FREE-2371 [60] => FREE-2372 [2763] => 102817_165 [2679] => 120517_182 [2791] => 062217_107 )

		$links = $this->paginate($avkeys, 24)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		//$tagArray = $this->getTags();
		View::share('tag', $string);

		return view('actor',['AvVideos' => $AvVideos, 'links' => $links, 'actor' => $string]);
	}

	//Banner

    /**
     * @param $string
     * @return \Illuminate\Http\RedirectResponse
     */
    public function banner($string){

        $string = \Crypt::decrypt($string);
        if (!$string) return redirect()->route('home');

        list($id, $url) = explode('|', $string);

        $banner = Banner::find($id);

        if(!is_null($banner)) {
            //記錄點擊
            if (Auth::check()) {
                $banner->view_2 += 1;
                $banner->save();
            } else {
                $banner->view_1 += 1;
                $banner->save();
            }

            //跳轉
            return Redirect::to($url);

        }else{
            return redirect()->route('home');
        }

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

		$links = $this->paginate($avkeys, 20)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}
		return view('search',['AvVideos' => $AvVideos, 'links' => $links, 'keyword' => $keyword]);

	}


	public function watch($avkey){
		//dd('2222222');
		$AvVideo = $this->getVideo($avkey);
		if (!$AvVideo) return redirect()->route('home');

		//is it my favorite video
		//處理該影片是否為我的收藏
		$boolMyFavoriteVideo = \App\Classes\Common::isMyFavoriteVideos($AvVideo);

		$VideoViews = 'VideoViews:'.$AvVideo['id'];
		Redis::select(1);
		Redis::incr($VideoViews);
		Redis::select(0);

		$key = 'MaybeYouLike-'.$AvVideo['avkey'];
		$MaybeYouLike = Redis::get($key);
		if (is_null($MaybeYouLike)) {
			$MaybeYouLike = array();

			$AvVideoTag = \App\AvVideo::where('enable','on')->where('video_source', $AvVideo['video_source'])->where('is_free',$AvVideo['is_free'])->where('video_type', $AvVideo['video_type'])->orderBy('updated_at', 'desc')->where('avkey','!=',$AvVideo['avkey'])->inRandomOrder()->take(12)->pluck('avkey')->toArray();			

			if ($AvVideoTag) foreach ($AvVideoTag as $_avkey){
				$video = $this->getVideo($_avkey);
				if ($video) $MaybeYouLike[] = $this->getVideo($_avkey);
			}

			$MaybeYouLike = json_encode($MaybeYouLike);
			Redis::setex($key, 3600, $MaybeYouLike);
		}
		$MaybeYouLike = json_decode($MaybeYouLike, true);

		//dd($MaybeYouLike);
		//dd($AvVideo);
		//dd(['AvVideo'=>$AvVideo,'MaybeYouLike'=>$MaybeYouLike,'RightBoxVideos'=>$MaybeYouLike,'boolMyFavoriteVideo'=>$boolMyFavoriteVideo]);

		//$AvVideo['m3u8_url'] = preg_replace('/^https\:\/\/"\+CN[0-9]\+"/','http://www.a.29cdn.com',$AvVideo['m3u8_url']);
		//$AvVideo['m3u8_url'] = preg_replace('/^https\:\/\/"\+CN[0-9]\+"/',env('SIHU_CDN_URL'),$AvVideo['m3u8_url']);
		return view('video',[
		    'AvVideo' => $AvVideo,
            'MaybeYouLike' => $MaybeYouLike,
            'RightBoxVideos' => $MaybeYouLike,
            'boolMyFavoriteVideo' => $boolMyFavoriteVideo,
            //'tagArray' => $this->getTags()
        ]);
	}

	private function previewToken($_fileName) {
		$ipLimitation = true; //加入ip認證
		$secret = "Av#Baby";             // Same as AuthTokenSecret
		$protectedPath = "/1000k/previews/";        // Same as AuthTokenPrefix
		//$hexTime = dechex(time());             // Time in Hexadecimal      
		//$hexTime = time() + (3600*3);             // 3 小時後過期
		$hexTime = time() + (3600);             // 3 小時後過期
		$fileName = $protectedPath.$_fileName;    // The file to access
		if (preg_match("/\.m3u8$/i", $fileName)) {
			$ip = $_SERVER['SERVER_ADDR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$token = md5($secret . $fileName . $hexTime . $ip); 
		$url =  $fileName . "?token={$token}&expire={$hexTime}";
		//return 'http://video.twdio.com'.$url;
		//return  'http://video.twdio.com:8081'.$url;
		return  'http://128.1.236.186:8080'.$url;
	}


	// get cartoon video infomation for database
	//  20190516
    public function getVideoCarInfo()
    {
		//dd('77777');
		
		$datas = \App\GetVideoInfo::orderBy('id','desc')
			->where('user_id',7)
			->where('method','DELETE')
			->get()
			->pluck('path')
			->toArray();
		$tempStr = '';


		foreach($datas as $data)
		{
			$tempStr = $tempStr . str_replace("admin/AvVideo/","",$data) . ",";
		}

		//dd($tempStr);

		$tempStr = substr($tempStr,0,-1);
		$tempStr = explode(",",$tempStr);
		$datas = \App\AvVideo::orderBy('id','desc')
			//->whereIn('id', $tempStr)
			->where('video_source', 2)
			->where('id', '=', 19401)
			->get()
			->toArray();

		//卡通20
		//$avkeyTitles = [['白浊女教师与臭男人们-下卷-DG-316', 'car-1808101'],['白浊之村上卷-DG-175', 'car-1808102'],['白浊之村下卷-DG-187', 'car-1808103'],['让我怀孕吧青龙君-第1话-和我生小孩吧-PXY-10057', 'car-1808104'],['自慰×2-CCYA-016', 'car-1808105'],['角色扮演露出研究会-第一天-WBR-043', 'car-1808106'],['角色扮演露出研究会-第二天-WBR-047', 'car-1808107'],['阴阳师-妖艳女神-淫乱咒缚-第二话-最后女神-DG-322', 'car-1808108'],['连结方式3rd-BLIND-DG-142', 'car-1808109'],['青梅竹马与同学-1限目-爱情lE悦乐-DG-127', 'car-18081010'],['独立调査官小泉-DG-212', 'car-1808141'],['独蛾-Counseling-2-觉醒-DG-231', 'car-1808142'],['玲-ZERO-1-逮捕特务搜查官-DG-314', 'car-1808143'],['玲-ZERO-Episode02-让女搜查官堕落成母猪-PXY-10054', 'car-1808144'],['疯狂训导主任断罪学园-诞生篇-DG-258', 'car-1808145'],['秘汤之旅-隠汤-上卷-青梅竹马是小老闆娘-HAO-008-1', 'car-1808146'],['相关游戏-2-后篇-DG-250', 'car-1808147'],['相关游戏-2-前篇-DG-228', 'car-1808148'],['美丽女将美惠-小料理屋的慕情-丧服人妻-第二卷-MGOD-0065', 'car-1808149'],['美熟女-前篇-DG-313', 'car-18081410']];
		//卡通21
		$avkeyTitles = [['装甲骑女伊莉丝-VOL-03-由战士转变为悦虐娼妇-DG-181', 'car-1808151'],['装甲骑女伊莉丝-VOL-04-战士的生还-DG-236', 'car-1808152'],['超昂闪忍春香-壹双龙轮-DG-223', 'car-1808153'],['满淫电车1-2-巨乳菁英-OL出发淫行-处女之路充满精液-安全日确认完毕-DD-041', 'car-1808154'],['满淫电车-说不清的放学后-急发射注意-DG-192', 'car-1808155'],['遗作-Respeet-第一-三幕-DV-011', 'car-1808156'],['燐月-全篇-DV-080', 'car-1808157'],['爆乳BOMB-1-护士立花薫-医院中有许多烦恼-DG-251', 'car-1808158'],['爆乳BOMB-2美女人妻-椎名美咲-白天的社区有许多秘密-DG-252', 'car-1808159'],['爆乳BOMB-3女教师若槻理沙-男生厕所充满危险男子トイレは危险がいっぱい-DG-253', 'car-18081510'],['鬼父-上卷-下卷-DD-057', 'car-1808161'],['渐进曲-天使们的私人课程-7-HAO-011-7', 'car-1808162'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-4-DG-259', 'car-1808163'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-8-JDXA-56848', 'car-1808164'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-9-JDXA-56849', 'car-1808165'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-10-JDXA-56850', 'car-1808166'],['被儿子的朋友侵犯-前篇-DG-184', 'car-1808167'],['被干斗士-疯狂性爱-DG-320', 'car-1808168'],['被阴湿阿宅弄到高潮的妹妹-女友-处女丧失-WBR-045', 'car-1808169'],['被阴湿阿宅弄到高潮的妹妹-女友-痴汉调教-WBR-049', 'car-18081610'],['银妖虫蚀-凌触岛退魔录-贰之卷-DG-134', 'car-1808171'],['装甲骑女伊利亚-VOL-02娼馆的女战士-DG-133', 'car-1808172'],['爆乳女僕狩猎-后篇-DG-214', 'car-1808173'],['爆乳女僕狩猎-前篇-DG-196', 'car-1808174'],['艶欲-微热-DG-012', 'car-1808175'],['魔法少女伊斯卡-Vol-01-邂逅-DG-323', 'car-1808176'],['魔法少女爱蕾娜-Vol-01-爱蕾娜要高潮了-VAA-0007', 'car-1808177'],['魔界天使吉布里尔-EPISODE2-VOL-1-DG-021', 'car-1808178'],['魔界天使-西普里尔-Vol-1-DG-232', 'car-1808179'],['魔界骑士英格利多-episode04-悽惨的下场-DG-324', 'car-18081710'],['青梅竹马与同学-2-爱情lE悦乐-DG-161', 'car-1808201'],['监狱战舰Vol-02-洗脑改造-DG-256', 'car-1808202'],['真燐月-上卷-真-祈祷篇编-DG-157', 'car-1808203'],['高潮之战-Vol-1-女格闘家散华-HAO-023-1', 'car-1808204'],['高潮之战-Vol-2-女忍者惨状-JDXA-0056852', 'car-1808205'],['鬼父-2-上卷-HAO-004-1', 'car-1808206'],['鬼父-2-下卷-HAO-004-2', 'car-1808207'],['鬼父-Re-birth-有点嚣张的屁眼-初回限定版-ACPDL-1023', 'car-1808208'],['通勤快乐-痴汉de-GO-DG-019', 'car-1808209'],['My妹-小恶魔A罩杯-下卷-跟哥哥做到忘我-MJAD-043', 'car-1808211'],['下级生2-季花词集-Anthology-第一节-脱脱莉-秘密-DAD-008', 'car-1808212'],['女高中生与议员-ACPDR-1041', 'car-1808213'],['午后的红潮-DPSM-9487', 'car-1808214'],['少女×少女×少女-THE-ANIMATION-第一幕-祭子-JDRA-57145', 'car-1808215'],['少女×少女×少女-THE-ANIMATION-第二幕-酒池肉林-JDXA-57142', 'car-1808216'],['开心狂干-4-QNB-006', 'car-1808217'],['奴隶巿场-SLAVEⅠ-Ⅲ-DV-016', 'car-1808218'],['爸爸之爱-巨乳美臀少女彩芽-天真天然好奇心-ACPDL-1037', 'car-1808221'],['轮姦俱乐部-第二话-堕落的爱子与山中知美-PXY-10061', 'car-1808222'],['雨芳恋歌娇涩淫蕩巨乳夏恋-火辣熟透的真夏果实-ACPDL-1036', 'car-1808223'],['美少女动画-DVD美少女DVD-Players-Game-型录-2004-', 'car-1808224'],['美熟母-后篇-JVDW-168R', 'car-1808225'],['钢铁魔女01-ZIZD-00001', 'car-1808231'],['捕获处女-上卷-纯洁的轮舞曲-MJAD-033', 'car-1808232'],['捕获处女-下卷-恶梦的重演-MJAD-039', 'car-1808233'],['桃色三国-第2话-长板之战-MJAD-020', 'car-1808234'],['热带之吻-烟火很漂亮吧-篇-ACCDL-1014', 'car-1808235'],['爱妻日记-JDXA-56871', 'car-1808236'],['秘书课堕落-THE-ANIMATION-JDXA-56963', 'car-1808237'],['耻辱诊察室-诊察1﹒2-淫狱病栋-DV-030', 'car-1808238'],['臭作-Replay-一-四夜-DV-012', 'car-1808239'],['谁都可以上她-1-GLOD-0027', 'car-18082310'],['鬼父-Re-born-有点嚣张的秘汤巡礼-旅情篇-ACPDL-1031', 'car-1808241'],['鬼父-上卷-嚣张的小热裤-DG-241', 'car-1808242'],['鬼作-THE-ANIMATION-一-六全收录-DV-001', 'car-1808243'],['乾妹妹-DAD-009', 'car-1808244'],['最终痴汉电车-全收录-DV-002', 'car-1808245'],['来强姦我吧-九条樱篇-ACCDR-1012', 'car-1808281'],['过错-OVA-第1话-随便你了-DG-248', 'car-1808282'],['邪恶女干部-第一话-掌握之力-觉醒-MGOD-0079', 'car-1808283'],['邪恶女干部-第二话-淫兽的调教-MGOD-0083', 'car-1808284'],['泳装女友-THE-ANIMATION-Fit-4-泳装与两名女友-DG-244', 'car-1808285'],['横恋母-Immoral-Mother-下卷-DG-245', 'car-1808286'],['潮吹美人鱼-游泳池畔的初体验-MGOD-0077', 'car-1808287'],['艶母全记录-DV-063', 'car-1808288'],['魔法少女爱蕾娜-Vol02-惠美留出击-VAA-0009', 'car-1808289'],['魔界骑士英格理特-Episode-02-紫色被虐-DG-242', 'car-18082810'],['痴汉十人队THE-ANIMATION-3-猎人的伤痕-DV-038', 'car-1809031'],['禁忌医院-Desire-1-JDRA-57087', 'car-1809032'],['跟真希干砲-纯真无垢天使-御园由希-篇-ACCDL-1013', 'car-1809033'],['跟真希干砲-闷骚淫乱少女-三条真希篇-ACCD-1010', 'car-1809034'],['雾谷伯爵家的六姊妹-第2话-闇之家族-JDXA-57062', 'car-1809035'],['穴里面的绝妙秘部-2-QNB-M008', 'car-1809111'],['后宫时间-THE-ANIMATION-Feast1-让你久等了-主人-JDXA-57131', 'car-1809112'],['蛊惑之刻-初回限定版-ACPDL-1029', 'car-1809113'],['馆-官能奇谭-2-ACPDL-1068', 'car-1809114'],['最终癡汉电车-NEXT-Molester-1-JDRA-57085', 'car-1809115'],['黑兽-高傲的圣女被白浊液体玷污-伺候国家对抗篇-ACJDL-0005', 'car-1809116'],['黑兽-高傲的圣女被白浊液体玷污-奥丽加×克洛维-黑之城-崩落篇-ACJDR-0002', 'car-1809117'],['新-胁迫2THE-ANIMATIONSCENE1-假动作-DAD-014', 'car-1809118'],['痴母全篇-DD-022', 'car-1809119'],['捕获处女-下卷-恶梦的重演-MJAD-039', 'car-1809131'],['就是算样我还是爱我老婆-第一话-MGOD-0075', 'car-1809132'],['就是算样我还是爱我老婆-第二话-MGOD-0081', 'car-1809133'],['惩罚指导-学园千金改性计画-File-01-DCLB-9232', 'car-1809134'],['惩罚指导-学园千金改性计画-File-02-DCLB-9326', 'car-1809135'],['惩罚指导-学园千金改性计画-File-03-DCLB-9402', 'car-1809136'],['最终痴汉电车-全收录-DV-002', 'car-1809137'],['最终癡汉电车-NEXT-Molester-1-JDRA-57085', 'car-1809138'],['寝取人妻-礼子-DBLG-9456', 'car-1809139'],['感染淫慾的连锁-山岸-优-篇-DG-240', 'car-18091310'],['IZUMO-一-四卷-DV-037', 'car-1809141'],['LOVE-2-Quad-ACPDR-1039', 'car-1809142'],['巨乳梦幻-爆乳宣言-编-ACJDL-0006', 'car-1809143'],['母猪公主-JDXA-56878', 'car-1809144'],['妖精公主妮娜-Vol-03-悪堕END-PXY-010064', 'car-1809145'],['扶他部-万裸温泉合宿编-WBR-073', 'car-1809146'],['来强姦我吧-九条樱篇-ACCDR-1012', 'car-1809147'],['淫缚学艳-全编-DV-025', 'car-1809148'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-11-JDXA-56853', 'car-1809149'],['渐进曲-天使们的私人课程-MY-BLOW-JOBER-ACT-12-JDRA-56842', 'car-18091410'],['女忍者学园忍法帖-完全版-DV-074', 'car-1809181'],['干砲练开发H游戏技巧-姫乃希沙良-开始恋爱篇-ACCDL-1008', 'car-1809182'],['干砲练开发H游戏技巧-溼答答-美少女製作人员篇-ACCDL-1009', 'car-1809183'],['才不是为了你才变大的-CCYA-018', 'car-1809184'],['少交女-THE-ANIMATION-Virgin-1-绚音与柚与哥哥-JDXA-57001', 'car-1809185'],['少交女-THE-ANIMATION-Virgin-2-和家教的淫蕩体验-JDXA-57002', 'car-1809186'],['开心狂干-1-QNB-002', 'car-1809191'],['开心狂干-2-QNB-004', 'car-1809192'],['巨乳乐园ー-爆乳女孩篇-ACJDL-0004', 'car-1809193'],['巨乳爱心-她在猛兽发情期-ACDDL-1005', 'car-1809194'],['妖艳女神-淫乱咒缚-第壹话-淫乱之咒-DG-247', 'car-1809195'],['我爱的弥生小姐-ZIZD-006', 'car-1809201'],['妹妹天堂-1-哥哥-来跟我干砲吧-GBR-001', 'car-1809202'],['妹妹天堂1-哥哥来跟我干砲吧-GBRR-001', 'car-1809203'],['妻子的妈妈-小百合-DMDW-9106', 'car-1809204'],['妻子的妈妈-小百合-后篇-DMDW-9111', 'car-1809211'],['姐SUMMER-下卷-MJAD-032', 'car-1809212'],['战乙女-G-第二话-贪淫-MGOD-0089', 'car-1809213'],['捕获处女-上卷-纯洁的轮舞曲-MJAD-033', 'car-1809214'],['人妻交奸日记-后篇-DG-235', 'car-1809261'],['人妻交换日记-前篇-DG-213', 'car-1809262'],['十二名女教师-后篇-DG-124', 'car-1809263'],['三角-BLUE-上卷-不能再-DG-224', 'car-1809264'],['下级生2-素描本-Page-1-Dress-DG-017', 'car-1809265'],['个人授业Lesson-2-密闭水槽-DG-129', 'car-1809266'],['大姊姊-MY-SWEET-ELDER-SISTER-THE-ANIMATION-senior-1-早纪学姐-JDRA-56291', 'car-1809267'],['大姊姊-MY-SWEET-ELDER-SISTER-THE-ANIMATION-senior-4-无法诚实面对-JDXA-56944', 'car-1809268'],['女友×女友×女友-与三姐妹的心跳一百同居生活-第二话-都会是厕所-保健室-女僕天堂-DG-326', 'car-1809269'],['女忍者咲夜-第一卷-女忍者捕缚-开始的调教-HAO-021-1', 'car-18092610'],['女忍者-咲夜-第二卷-浑身白浊液体的那雾女忍者-MGOD-0069', 'car-1809281'],['女武神调教精液瓶的战斗少女10姊妹-上卷-DG-262', 'car-1809282'],['女高中生与淫行教师4-读者模特儿静歌-高傲模特儿的性爱白书-ACPDL-1024', 'car-1809283'],['女教师大全集-DV-019', 'car-1809284'],['女僕姊姊-第一天-DMDW-9102', 'car-1809285'],['女僕姊姊-第二天-DMDW-9108', 'car-1809286'],['小爱-下卷-DG-334', 'car-1809287'],['山姫之花-真穂-DBLG-8996', 'car-1809288'],['不平衡-血腥生存战-1-3-DV-042', 'car-1809289'],['不能说的事-THE-ANIMATION-DBLG-10478', 'car-18092810'],['少女蹂躏游戏-上卷-DG-331', 'car-1810011'],['幻梦馆-全卷-人形之馆-DV-029', 'car-1810012'],['月狂猪病-第一夜-DG-195', 'car-1810013'],['水手服心理治疗人妻科-后篇-JVDW-178', 'car-1810014'],['主治医生的淫谋-后篇-JVDW-176R', 'car-1810015'],['Sweet-Home-你喜欢淫蕩的姊姊吗-二甘-DCLB-9016', 'car-1810031'],['Sweet-Home-你喜欢淫蕩的姊姊吗-三甘-DCLB-9072', 'car-1810032'],['Swing-Out-Sisters-vol-1-COMIC-2424', 'car-1810033'],['Tentacle-and-Witches-第1话-我想变成触手-PXY-10056', 'car-1810034'],['Tentacle-and-Witches-第3话-法斯特的陷阱-PXY-10063', 'car-1810035'],['Tonyrsquos-女主角系列-她是新娘候补生-灰姑娘精选ACT-1-DG-238', 'car-1810051'],['TSF物语-Trans-1-变成女生的身体的话该怎幺办-JDXA-57005', 'car-1810052'],['TSF物语-Trans-2-痴汉-轮姦-肉便器-JDRA-56996', 'car-1810053'],['一起干砲吧-结衣-葵篇-DG-312', 'car-1810054'],['人工少女-变身性爱-机器人-前篇-DG-178', 'car-1810055'],['从顺-催眠彼女-上-MJAD-099', 'car-1810056'],['公主限定-个性强硬的公主奥莉维亚-高傲且羞耻的脚指-ACPDL-1035', 'car-1810057'],['公主情人-上卷-HAO-003-1', 'car-1810058'],['公主情人-下卷-HAO-003-2', 'car-1810059'],['RAID-灵奴-第一章-侵入者-GH-046', 'car-1810111'],['RanrarrSem-白浊人妻-一之瀬杏奈-阿鼻叫唤篇-ACMDA-1047', 'car-1810112'],['RanrarrSem-白浊人妻-一之瀬莉子-自己解放篇-ACMDA-1046', 'car-1810113'],['Reunion-理英老师的个人授课-可爱的姊姊女友-初回限定版-ACPDL-1025', 'car-1810114'],['Reunion-摩穂穿不整齐的制服-冷酷的马尾的哀伤要求-ACPDL-1028', 'car-1810115'],['School-I-HBR-016', 'car-1810116'],['School-II-HBR-019', 'car-1810117'],['SION-VOLUME-02-陷入困境的魔法少女-DG-145', 'car-1810118'],['STARLESS-I-背德之馆-WBR-058', 'car-1810119'],['只对你说晚安-桃音-DMLK-9040', 'car-1810121'],['只对你说晚安-第一话零子-DG-218', 'car-1810122'],['只对你说晚安-第二话-誓约-DG-239', 'car-1810123'],['只对你说晚安-零子-DMLK-9208', 'car-1810124'],['叫你的名-上卷-DG-159', 'car-1810125'],['叫你的名-下卷-DG-183', 'car-1810126'],['外貌协会-Face-1-因为是头一次-要温柔一点-JDXA-56961', 'car-1810127'],['外貌协会-Face-2-女僕与主人与二号-JDXA-56962', 'car-1810128'],['奴隶女僕公主-vol-03-最后的调教-DG-152', 'car-1810129'],['奴隶女僕公主-Vol-04-调教完成-DG-197', 'car-18101210'],['奴隶看护-快乐的-乐园的尽头-1-3合集-DVA-001', 'car-18101211'],['对魔忍浅葱vol-03-姊妹相杀-DG-143', 'car-18101212'],['对魔忍浅葱-Vol-4-黑暗中的女忍者-DG-137', 'car-18101213'],['巨乳王者48-第二话-DG-311', 'car-18101214'],['巨乳生活前篇-DG-149', 'car-18101215'],['必杀癡汉人-后篇-DG-163', 'car-18101216'],['必杀癡汉人-前篇-DG-140', 'car-18101217'],['伪娘-千金小姐-小光与绫奈的祕密精选-CCYA-017', 'car-18101218'],['同学的妈妈-前篇-DG-006', 'car-18101220'],['出租舔舐-THE-ANIMATION-JDXA-57301', 'car-1810161'],['名媛女友性爱-1-DMDW-09116', 'car-1810162'],['名媛女友性爱-2-DMDW-9119', 'car-1810163'],['她不来探病的理由1-在心爱的面前-DG-341', 'car-1810164'],['她不来探病的理由-2-无尽的狂宴-MGOD-0072', 'car-1810165'],['宇宙海贼沙拉-VOL03-狂宴的双性骑士-DG-220', 'car-1810166'],['宇宙海盗沙拉-Vol-02-虏囚的被虐骑士-DG-164', 'car-1810167'],['有点小缝-Vol-1-DG-024', 'car-1810168'],['你不知道的护士-性的病栋24时-第二话-私を永远に汚して-DG-246', 'car-1810169'],['拥抱你-VOL-2-DG-222', 'car-18101610'],['亲吻花瓣-与恋人手牵手-DG-318', 'car-18101611'],['姫骑士莉莉亚-VOL-05-悦狱-雾子与蕾萝-DG-328', 'car-18101612'],['公主骑士莉莉亚-Vol-06-被魔色污染的最后-DG-317', 'car-1810171'],['双人性爱-2nd-DV-023', 'car-1810172'],['天空的颜色水的颜色-全篇-完全珍藏版-DD-051', 'car-1810173'],['少女赛克特-Innocent-Lovers-第二小时-DG-147', 'car-1810174'],['少女赛克特-Innocent-Lovers-第三小时-DG-158', 'car-1810175'],['少女赛克特-Innocent-Lover-第一小时-DG-130', 'car-1810176'],['主治医生的淫谋-前篇-JVDW-175R', 'car-1810177'],['仕舞妻-姊妹妻3全篇-DD-050', 'car-1810178'],['学园姊妹I-DG-126', 'car-1810251'],['学园姊妹II-DG-156', 'car-1810252'],['学园催眠奴隶-1-你真的是最差劲的垃圾-DG-321', 'car-1810253'],['学园催眠奴隶-2-不行了-要被中出到子宫去了-DG-330', 'car-1810254'],['拟态催眠-后篇-JVDW-174', 'car-1810255'],['放学后-湿透的制服-THE-BEST', 'car-1810256'],['放课后2-纱由理-DG-131', 'car-1810257'],['泳装女友-Fit-2-白色诱惑-DG-194', 'car-1810258'],['泳装女友-THE-ANIMATION-Fit-3-DG-225', 'car-1810259'],['傲娇淫蕩少女-掬水-2-WBR-056', 'car-1810261'],['傲娇淫蕩少女-掬水-WBR-052', 'car-1810262'],['徬徨淫乱的疯狂症状前篇-DG-176', 'car-1810263'],['感染-淫欲的连锁-佐伯瞳篇-DG-257', 'car-1810264'],['像妈妈吗-后篇-DG-243', 'car-1810265'],['像妈妈吗-前篇-DG-221', 'car-1810266'],['横恋母-Immoral-Mother-上卷-DG-229', 'car-1810267'],['横恋母-全篇-DD-056', 'car-1810268'],['渐进曲-渐慢曲-全篇-DD-052', 'car-1810291'],['渐慢曲-堕落天使们的呢喃-CONTENTS-2-in-the-school-DG-011', 'car-1810292'],['喜欢-喜欢-最喜欢了-THE-ANIMATION-Pretty-1-比你厉害-浮空-JDRA-57095', 'car-1810293'],['喜欢-喜欢-最喜欢了-THE-ANIMATION-Pretty-2-浮空-双子心-JDRA-57096', 'car-1810294'],['强暴-强暴-强暴-Rape-Rape-Rape-V-01-最初の牺牲者-DG-150', 'car-1810295'],['强暴-强暴-强暴-Rape-Rape-Rape-Vol-02-全白的黑暗-DG-177', 'car-1810296'],['催眠术2-ndversion2-往幻想与淫欲的领域-DG-179', 'car-1810297'],['催眠学园-后篇-DG-162', 'car-1810298'],['催眠学园-前篇-DG-165', 'car-1810299'],['洗衣店小信-第二话-DG-016', 'car-1810301'],['凌辱芳邻餐厅调教菜单-上卷-不可以着急-HAO-007-1', 'car-1810302'],['凌辱芳邻餐厅调教菜单-下卷-妹萌美-HAO-007-2', 'car-1810303'],['夏虫THE-ANIMATIONmolester-1-巴-DG-260', 'car-1810304'],['夏虫-THE-ANIMATION-molester-2-冬虫花葬-DG-327', 'car-1810305'],['姬奴隶-堕落成母猪的双胞胎公主牝へと堕ちゆく双子の王女-第二幕-魔物の子种を堕とす丽姬の哀-DG-155', 'car-1810306'],['家教大姐姐-2-教你色情的偏差值-Instruct-1-HAO-005-1', 'car-1810307'],['桃色三国-第1话-桃园之誓-MJAD-015', 'car-1810308'],['假装忘记-后编-DANM-10484', 'car-1810309'],['剑芒罗曼史-Ⅱ-六回收录-DV-008', 'car-1811021'],['姦染-3-首都崩坏-神凪悠帆篇-DG-333', 'car-1811022'],['姫骑士安洁丽卡-第2夜-复仇的赛菲娜-DG-128', 'car-1811023'],['度假胜地巨乳-第3话-南岛的羞耻篇-DG-193', 'car-1811024'],['思春期少女下卷-DG-132', 'car-1811025'],['战斗少女女武神-2第一话-堕天使的女神们-DG-144', 'car-1811026'],['战斗少女女武神-2-第二话-背叛的母奴隶-DG-180', 'car-1811027'],['战斗少女希维儿-Vol-03-绝望的轮姦-DG-169', 'car-1811028'],['战斗少女希维儿-vol-04-奴隶的新娘-DG-227', 'car-1811029'],['春恋-少女-在少女的庭园见面吧-后篇-DG-125', 'car-1811051'],['御魂-忍-卷の二-DG-230', 'car-1811052'],['御魂-忍-卷之一-DG-182', 'car-1811053'],['敏感运动员第一话-DG-254', 'car-1811054'],['梦喰-鹤见区式游戏製作-樟叶悠美-开发篇-ACMDA-1049', 'car-1811055'],['梦喰-鹤见区式游戏製作-樟叶瑠美-调教编-ACMDA-1048', 'car-1811056'],['淫妖虫-悦-怪乐变化退魔录-第二夜-DMLK-9100', 'car-1811057'],['淫辱调教-女僕-继母-DD-049', 'car-1811058'],['淫兽学园-La-Bluegiri-复活篇-四回收录-DV-014', 'car-1811059'],['15美少女漂流记-南岛爽快尽情干砲篇-HAO-001-3', 'car-1811061'],['-160protected-RJ041616', 'car-1811062'],['ASGALDH-被曲解的遗嘱-一-三章全记录-DV-010', 'car-1811063'],['BABUKA-极道之妻-沼尻惠理香-发掘色情变态的性癖-DCLB-9206', 'car-1811064'],['Bible-Black-La-lanza-de-Longinus-全五话-DV-083', 'car-1811065'],['Bible-Black-圣经黑书外传1-外传2黑之刻印-黑之祭坛全篇-DD-016', 'car-1811066'],['Bible-Black-圣经-黑书-黑魔术的学园-第一-六章-DV-013', 'car-1811067'],['BLIND-NIGHT-盲目之夜-DV-043', 'car-1811068'],['Bloods-淫落的血族-2-天然冷酷香夜-妖艶的甜蜜黑色长髮-ACPDL-1030', 'car-1811069'],['Floating-Material-下卷-我喜欢-这种的-ACPDL-1022', 'car-1811081'],['Grope-黑暗中的小鸟们-2nd-S-ACE-DG-136', 'car-1811082'],['HAMPUBANGU-DPSM-9458', 'car-1811083'],['HHH-三重性爱-小波篇-HAO-018-2', 'car-1811084'],['HHH-三重性爱-美雪篇-MJAD-023', 'car-1811085'],['H-性爱性爱-DG-234', 'car-1811086'],['姐汁-2-THE-ANIMATION-交给白川三姐妹-Liquid-1-帮你自慰-HAO-006-1', 'car-1811091'],['姐姐-怀孕MIX-VOL-03-让姐姐怀孕-DG-007', 'car-1811092'],['姐姐-振作一点啦-第4话-DG-015', 'car-1811093'],['学园-3-华丽的悦辱-THE-ANIMATION-EPISODE01-DCLB-8974', 'car-1811094'],['学园-3-华丽的悦辱-THE-ANIMATION-EPISODE-02-DCLB-9068', 'car-1811095'],['渐进曲-天使们的私人课程-6-DG-329', 'car-1811096'],['渐进曲-渐慢曲-ULTIMATUM-SERA-2-各自的日常-DG-138', 'car-1811097'],['渐进曲-渐慢曲-ULTIMATUM-SERA-3-序章-终章-DG-151', 'car-1811098'],['催眠凌辱学园-第一话-疑似体验术-DG-167', 'car-1811099'],['My妹-小恶魔A罩杯-上卷-最讨厌大哥哥了-MJAD-036', 'car-1811121'],['OVA-深红女孩-痴汉支配-WRDK-001', 'car-1811122'],['OVA-新婚淫妻的HONEY-DAYS-2-KNB-M002', 'car-1811123'],['Stretta-Contents-2-RUMA-DG-216', 'car-1811124'],['吸血鬼-第一夜-DMDW-9104', 'car-1811125'],['姊妹-第1章-Square-Sisters-DG-135', 'car-1811126'],['姊妹-第2章-Triangle-Lovers-DG-166', 'car-1811127'],['euphoria-莳羽梨香-葵菜月-苏醒的地狱绘图编-ACJDL-0012', 'car-1811131'],['First-Love-千夏-DMDW-9118', 'car-1811132'],['First-Love-香澄-DMDW-9114', 'car-1811133'],['Floating-Material-上卷-明明不能这样做-我却-HAO-022-1', 'car-1811134'],['IF-VALIANT-SIN-FAY-GH-007', 'car-1811135'],['Imbalance-Girl-不平衡女孩-GH-037', 'car-1811136'],['Innocent-Blue全篇-DD-043', 'car-1811137'],['KAGIROHI-白景-上卷-地狱触手-DG-249', 'car-1811138'],['Bloods-淫落的血族-2-迷你美咲-非常坚持的过膝袜-ACPDL-1026', 'car-1811141'],['Brandish-第1话-获得可爱的男孩-MJAD-037', 'car-1811142'],['Brandish-第2话-MJAD-041', 'car-1811143'],['CAFEacute-JACKY-1st-Cafe-Macchiato-DG-153', 'car-1811144'],['Dark-Blue-Vol-1-被盯着的羞耻-ACPDL-1034', 'car-1811145'],['DISCIPLINE-第一章ndash第四章-DV-032', 'car-1811151'],['DreamNote-第一话-DG-226', 'car-1811152'],['Dream-Note-第一话-MGOD-0041', 'car-1811153'],['Dream-Note-第二话-堕落家族-乱交开始-MGOD-0073', 'car-1811154'],['euphoria-帆刈叶-楽园终焉篇-ACJDL-0003', 'car-1811155'],['Love-Selection-Seiect-1-Love-Selection-DG-139', 'car-1811161'],['Love-Selection-THE-ANIMATION-Select-2-Favorite-Menu-DG-160', 'car-1811162'],['MAGICWITCH-ACADEMY-DG-047', 'car-1811163'],['milkyBEST-SELECTION-Vol-2-DD-045', 'car-1811164'],['姐-孕-MIX-Vol-特别篇-ANE-HARA-MIX-DG-123', 'car-1811165'],['（3D）泳池與爆乳美女激戰！！肉棒快被榨干', 'car-1811221'],['3D 鐘樓怪人也需要發洩', 'car-1811222'],['3D 魔獸世界 精靈愛狼屌', 'car-1811223'],['宇宙奴隷艦アマド 異種姦孕まされ奴隷', 'car-1811224'],['制服絲襪系列 第10彈－肉絲中國風', 'car-1811225'],['3D 好吃的阿凡達香腸', 'car-1811231'],['3D 觸手侵入美少女戰士 individual tentacle2', 'car-1811232'],['Fate同人-聖女陥落 処女戦士に襲いかかる狂気の兵士達(甲冑)', 'car-1811233'],['火影同人', 'car-1811234'],['海賊王h同人', 'car-1811235'],['鬼父 Rebuild Vol.2「清楚に跨がるコスプレニート', 'car-1811236'],['[3D高清無修] 國人自制 制服絲襪系列 第1彈－肉絲御姐', 'car-1811261'],['[3D高清無修] 國人自制 制服絲襪系列 第2彈－白絲學生', 'car-1811262'],['[3D高清無修] 國人自制 制服絲襪系列 第3彈－網絲人妻', 'car-1811263'],['3D 大屌蕾絲邊互捅', 'car-1811264'],['3D 魔獸世界 龍女 Alexstrasza', 'car-1811265'],['3D同人《尼爾：自動人形》', 'car-1811266'],['3D同人《生死格鬥》穂乃香', 'car-1811267'],['哥特蘿莉', 'car-1811268'],['[3D高清無修] 國人自制 制服絲襪系列 第4彈－黑絲兔女郎', 'car-1811271'],['[3D高清無修] 國人自制 制服絲襪系列 第5彈－白絲天使', 'car-1811272'],['[3D高清無修] 國人自制 制服絲襪系列 第6彈－黑絲精靈', 'car-1811273'],['3D 7 Days - Episode 1 觸發', 'car-1811274'],['3D 7 Days - Episode 2 背叛', 'car-1811275'],['3D 7 Days - Episode 3 改變', 'car-1811276'],['3D Akata 動物狗猩猩強暴獸交', 'car-1811277'],['3D 幽靈古堡 jill valentine 被巨型屌殭屍強姦', 'car-1811278'],['3D 跟腿一樣長的妖獸屌', 'car-1811279'],['小女子交配蠻牛', 'car-18112710'],['3D 姊妹相姦 3', 'car-1811281'],['3D 姦牢', 'car-1811282'],['3D 幽靈古堡 生化獸姦實驗室', 'car-1811283'],['3D無碼 被馬的大雞巴狠狠抽插', 'car-1811284'],['DQ女戦士～陵辱無限回廊～', 'car-1811285'],['体操服姿のむっちりJKが自宅でケツを向けて腰振りダンスを披露する3Dアニメ', 'car-1811286'],['催眠☆學園 ～忖度女教師の教卓処女給◆～', 'car-1811287'],['蘿莉們在廢土的生活20', 'car-1811288'],['蘿莉被當狗幹', 'car-1811289'],['[3D]寢取られ秋葉様　チアコス亂交編', 'car-1811291'],['[3D]寢取られ秋葉様、放課後お散歩編', 'car-1811292'],['[3D]遠野秋葉ノ男性事情～　寢取られ秋葉様、中年オヤジとラブホ編', 'car-1811293'],['3D Ashley and Femshep 接受殘酷的怪獸受精實驗', 'car-1811294'],['3D Samus Aran 殭屍，怪獸，機械人的發洩工具', 'car-1811295'],['3D 幽靈古堡 Moira Burton 下面讓黑人開了個大洞', 'car-1811296'],['3D 觸手雙穴齊發', 'car-1811297'],['3D同人《薩爾達傳說》', 'car-1811298'],['[3D Hentai] (komiroto) rakicheri 豐胸交溝', 'car-1811301'],['[3D hentai] AKUMA NO OSHIGOTO 飢渴的魔女們 (studioGGB) 高清', 'car-1811302'],['[3D](某科學超電磁砲) Play Home Misaka Mikoto Hentai', 'car-1811303'],['[3D高清無修] 國人自制 制服絲襪系列 第7彈－網絲御姐', 'car-1811304'],['[3D無盡]可愛的游泳女孩性交在游泳池', 'car-1811305'],['[18禁][PV] キモヲタ教師が、可愛い女生徒に 性活指導!!', 'car-1811306'],['[繁體]ぽんぷりん Reflect 3D', 'car-1811307'],['[寶可夢] 露莎米奈&竹蘭', 'car-1811308'],['(3D)とらぶるだいあり～しすたぁ', 'car-1812031'],['[3D高清無修] 國人自制 制服絲襪系列 第8彈－蕾絲女警', 'car-1812032'],['3D 2018最新電玩美穴', 'car-1812033'],['3D 獸交大師 26regionsfm 作品大合集', 'car-1812034'],['火影忍者-日向雛田', 'car-1812035'],['3D 等待被神騎的母雞', 'car-1812041'],['3D 進擊的巨人 小米卡桑的淫亂日記', 'car-1812042'],['3D 魔獸世界 黑暗精靈 Sylvanas 和獸人的外交手段', 'car-1812043'],['3D(東方Project)霊夢封淫 禁斷の輪姦地獄篇', 'car-1812044'],['3Dの爆乳金髪アニメキャラがフル勃起チンポを挾んで優しくパイズリご奉仕', 'car-1812045'],['3D Hentai 機械章魚 生章魚 變態男 子宮侵入', 'car-1812051'],['3D Hentai 癡女戰士的凌辱試煉 Enlistment', 'car-1812052'],['3D honey girl fucking each other', 'car-1812053'],['3D MMD 女囚犯的SM體罰', 'car-1812054'],['3D MMD 觸手抽插亂鬥飢渴女2', 'car-1812055'],['3D Suima Episode II', 'car-1812056'],['3D 妖獸濕濕粗粗長長的屌 01', 'car-1812057'],['3D 異種姦 Bio Seeker PHI-D9 A社2019最新力作', 'car-1812058'],['第1集 ー 亂倫 ー 哥哥你想要什麼', 'car-1812061'],['第2集 ー 淫亂', 'car-1812062'],['無刪減版【日語／繁中】2018十月新番【哥布林殺手】07', 'car-1812063'],['感染ソドム セル版', 'car-1812064'],['溫泉之旅~中文字幕★S-CUTE+巨乳ドスケベ+蘿莉+STP嚴選極品成人系列', 'car-1812065'],['蘭斯01 光をもとめて 第4話「そして、王道', 'car-1812066'],['Skyrim Immersive Porn - Episode 12', 'car-1812071'],['Studio Izumo concept work', 'car-1812072'],['Sweet Home ～色情的姐姐喜歡嗎？', 'car-1812073'],['UMEMARO 3D HONRY GIRL', 'car-1812074'],['VIPER -GTS- 第2話 「悪魔交輪篇」', 'car-1812075'],['[3D] 地牢中被淫虐的女戰士', 'car-1812101'],['[3D繁]BEYOND-2nd REPORT- HDリマスター', 'car-1812102'],['[3D繁]淫欲に溺れる人妻-百合子-清く美しかった母が一人の女に還る刻', 'car-1812103'],['[3D繁]清純系アイドル インモラル撮影會[水手服]', 'car-1812104'],['3D [MiMiA Cute] futa futa 1 - 給學妹灌滿獎學精', 'car-1812105'],['3D Bloodlust Cerene - Royal Descent 第二部 自生女大屌爆插公主', 'car-1812106'],['3D Bloodlust Cerene - Royal Descent 第三部 女吸血鬼加粗加長分身3P', 'car-1812107'],['3D Bloodlust Cerene Royal Descent 嗜血女幹翻皇家女', 'car-1812108'],['3D Girlfriend 我的3D女友', 'car-1812109'],['3D hentai 7 Days - Episode 4 墮落天使', 'car-18121010'],['3D Support Girl', 'car-1812111'],['3D 女忍者的觸手之術 ĸυɴoιcＨι ѕαyo', 'car-1812112'],['3D 淫獸莊園招待狀', 'car-1812113'],['3D 滿月 打開無間淫獸地獄之門', 'car-1812114'],['3D 漁人的嫩穴', 'car-1812115'],['3D Hentai 快點魯莽的插那裡 !!! Toraburudaiari Harenchi', 'car-1812121'],['3D Hentai 被卡在公園當公廁的學姊 Rina and Ana', 'car-1812122'],['3D MMD看完忽然間覺的蛋疼', 'car-1812123'],['3D 女忍者的陰部訓練', 'car-1812124'],['3d 迷你蘿莉3', 'car-1812125'],['3D 強制姦淫調教 太空戰士 TIFA', 'car-1812126'],['3D 淫蕩羅莉彩虹娜娜 夏天開鮑樂', 'car-1812127'],['(3D)BLOODLUST CERENE - ROYAL DESCENT', 'car-1812131'],['(3D)痴漢した女子●生とその後、むさぼり合うようなドエロ純愛 part.1', 'car-1812132'],['(3D)蘭子淫亂狂欄 水著', 'car-1812133'],['[3D][簡中] 睡魔 Episode2 覚醒', 'car-1812134'],['[3D][同人] 耳交', 'car-1812135'],['[3D][同人][貓女]學校の怪姦 尻子玉を狙って少女に襲いかかるカッパ達', 'car-1812136'],['[3D]梅麻呂系列 廁所小便后再內射几發', 'car-1812137'],['[3D]清純系アイドル インモラル撮影會[競泳服]', 'car-1812138'],['[3D高清無修] 國人自制 制服絲襪系列 第9彈－黑絲御姐', 'car-1812139'],['[3D無碼]哈利波特系列之一', 'car-18121310'],['[3D無碼]哈利波特系列之二', 'car-18121311'],['OVAそれでも妻を愛してる', 'car-1812141'],['Taimanin Doujin good', 'car-1812142'],['Tiny Evil 幼淫魔 1+2話 合集 無刪減', 'car-1812143'],['おっぱいカフェ～母娘でコスちち The Motion', 'car-1812144'],['清純系アイドル2 股間節柔軟の激痛に嗚咽', 'car-1812145'],['痴漢した女子●生とその後、むさぼり合うようなドエロ純愛 part.2', 'car-1812146'],['3D 魔獸統治世界的第一步就是不斷繁殖後代', 'car-1812171'],['3D(女學生)', 'car-1812172'],['3D火影忍者莎羅娜', 'car-1812173'],['3061=漫畫3D=無碼=巨乳護士得特別服務', 'car-1812174'],['3063=漫畫3D=爆乳高中生愛打砲', 'car-1812175'],['3064=漫畫3D=上集=住院時好色的護士找我打砲', 'car-1812176'],['DISCODE 異常性愛 Part-1', 'car-1812177'],['あらいめんとゆーゆー THE ANIMATION Ghost.1 「丸いお尻がゆるせない☆」', 'car-1812178'],['ある日、弟が覗き見たのは不良たちに昏睡○○○される大好きだった姉の姿だった', 'car-1812179'],['エルフ姫ニィーナ Vol.01 淫城に囚われし麗姫', 'car-18121710'],['(18禁アニメ) 霧谷伯爵家の六姉妹 第1話「霧の華族」 (日語中字)', 'car-1812181'],['(18禁アニメ) 霧谷伯爵家の六姉妹 第2話「闇の家族」 (日語中字)', 'car-1812182'],['[3D繁]Enlistment 痴女と戦士と卑猥な試練', 'car-1812183'],['[18禁][鈴木みら乃 petit]かぎろひ～勺景～ Another 第一夜 少女との蜜月、その終わり', 'car-1812184'],['[18禁][鈴木みら乃] かぎろひ～勺景～ Another 第二夜 夕暮れの教室、誘う艶髪', 'car-1812185'],['3D Delusion World 超視姦', 'car-1812186'],['3D 群魔亂姦', 'car-1812187'],['H動漫=近親相姦=哥哥跟妹妹在浴室打砲', 'car-1812188'],['【3D】【繁】魔法少女オリハルコン[BIG5]', 'car-1812191'],['【MMD】3D少女前線HK416吸い込みローリングフェラチオ射精管理オナニー', 'car-1812192'],['3D瑪莉螺絲觸手貫穿', 'car-1812193'],['巨乳ドスケベ學園 上巻 乙女クラブの秘密', 'car-1812194'],['母娘丼 おっぱい特盛母乳汁だくで[中文字幕]', 'car-1812195'],['坐姿大屌插入粉嫩蜜穴', 'car-1812196'],['姉、ちゃんとしようよっ！Vol.01', 'car-1812197'],['花粉少女注意報！～THE ANIMATION～ ATTACK No.2「ヌルヌルでドロドロが止まらないっ♪」', 'car-1812198'],['[3D動畫]梅麻呂系列 (13) 精液檢查', 'car-1812201'],['[家庭菜園] いめちぇん!妹しすた〜!', 'car-1812202'],['[梅麻呂3D] 淫亂爆乳女教師', 'car-1812203'],['[梅麻呂3D]杉本翔子のSexyトレーナー', 'car-1812204'],['[梅麻呂3D]淫亂診察室', 'car-1812205'],['【3D】【繁】[マーマレード★スター]とらぶる・だいあり～ ムービー版', 'car-1812206'],['3D  木之內3：ダークバタフライ', 'car-1812207'],['3D dokidokiりとる大家さん 2nd', 'car-1812211'],['3D 小蘿莉的性體驗 Sex of Wakana', 'car-1812212'],['3D 觸手巨根鬼王暴虐嫩穴 !', 'car-1812213'],['Fairy Heart美眉戰敗場景合集', 'car-1812214'],['H動漫中字-滿淫電車-調書(粉紅女教師電車癡漢中出 長的像拉克絲 )', 'car-1812215'],['OVA大好きな母 ＃2 大好きな母の裏側', 'car-1812216'],['神曲的魔導書 02 「強欲的魔導書」 THE ANIMATION 第二章「強欲の魔導書」', 'car-1812221'],['鬼父 Rebuild Vol.3「小生意気な萌顔ほぃほぃ❤」', 'car-1812222'],['推薦H漫 10 (受個傷能遇到這種看護都值了 01)', 'car-1812223'],['梅麻呂系列 廁所小便后再內射几發', 'car-1812224'],['淫行教師4 feat.エロ議員センセイ 靜歌＆初音～ガラス越しの背徳取調', 'car-1812225'],['[H動漫] 清純長髮巨乳女被朋友帶去多P內射', 'car-1812251'],['［H動漫］少女戦機_合集（沒刀過）', 'car-1812252'],['［H動漫］宇宙海賊サラ_1~4合集（沒刀過）', 'car-1812253'],['淫蕩遊戯Ω(前編)～闇の眷族vs女ドラゴン', 'car-1812254'],['淫蕩遊戯Ω(後編)～神の力', 'car-1812255'],['[3D おっとりサキュバスちゃん 性春白書', 'car-1812261'],['[3D] (無修)Yoshiwara Rose 2 A Cycle Of Guilt', 'car-1812262'],['[3D] バクドリ ～寢取り種付け母娘サンド～ the movie', 'car-1812263'],['[3D] 淫艶の湯～三代の女將達との密交 The Motion Anime', 'car-1812264'],['[3D]Eralin & Meralin Trailer', 'car-1812265'],['[3D]Ghosts of Paradise', 'car-1812266'],['[3D]みこぱこ!しゅららちゃん もふもふロリ巫女とのイチャラブセックスライフ', 'car-1812267'],['[3D]メイド系過激リフレ 本日開店!', 'car-1812268'],['[3Dエロアニメ] お兄ちゃんの半分は欲望でできています1.2 Motion Movie', 'car-1812269'],['[3Dエロアニメ] ディーナのバーチャル催眠の誘惑版', 'car-18122610'],['[3Dエロアニメ] 動くけもみこ! 壱', 'car-18122611'],['［H動漫］淫妖蟲 THE ANIMATION 合集（沒刀過）', 'car-1812271'],['［H動漫］陰陽師～妖艶絵巻～合集（沒刀過）', 'car-1812272'],['［H動漫］魔法少女アイ參_合集（沒刀過）', 'car-1812273'],['3D star atlas 各電玩動漫女角開苞大亂鬥', 'car-1812274'],['とらぶるだいあり～・ごーるど (3500kbps)3D', 'car-1812275'],['梅麻呂3D動畫系列04-舞～Mai～', 'car-1812276'],['[3Dエロアニメ] nekomimi escort girl', 'car-1812281'],['[3Dエロアニメ] Oh,Yes！ 褐色ビッチ人妻の性欲..ロできるママさんバレー會～ The Motion Anime', 'car-1812282'],['[3Dエロアニメ] チアコスエッチしよっ動畫版', 'car-1812283'],['フリフレ2 濁妹・菫～ハメ注ぐ血路の滴り～', 'car-1812284'],['レイプ合法化っ！！！ act.1 いつでもどこでも犯しまくるっ！', 'car-1812285'],['日本-H卡通美少女成人動漫畫3D', 'car-1812286'],['[3D]僕の教え子はビッチギャル', 'car-1901021'],['[3Dエロアニメ] [貓拳]ふたとも!華と茉結', 'car-1901022'],['[3Dエロアニメ] Severance', 'car-1901023'],['水龍敬ランド ＃2', 'car-1901024'],['春香に逢います アイドル痴漢電車 陵辱急行第2車', 'car-1901025'],['凌辱的連鎖 ー 前編', 'car-1901026'],['[3Dエロアニメ] ある日、ネットで見つけ..撮りされた彼女の動畫だった', 'car-1901031'],['[3Dエロアニメ] エッチに興味津々な彼女と純愛SEX', 'car-1901032'],['[3Dエロアニメ] とらぶるだいあり～・はれんち', 'car-1901033'],['[3Dエロアニメ] パパ♪ちりょうのおじかんだよ♪本編', 'car-1901034'],['[3Dエロアニメ] 古書堂奇譚 鷺沢無情[泳裝版]', 'car-1901035'],['がオジさんチ○ポとじゅぽじゅぽいやらしいセックスしてます。＃2オジさんチ○ポ、みんなでシェア', 'car-1901036'],['せるふぃっしゅ] レイプ合法化っ！！！ act.2 わたしたち幸せです・・ご主人様', 'car-1901037'],['せるふぃっしゅ] 痴漢した女子●生とその後、むさぼり合うようなドエロ純愛 part.2', 'car-1901038'],['と○ぶるだいあり～・ぴーち ムービー版(4000kbps)3D', 'car-1901039'],['[3Dエロアニメ] マジメな姪に催眠術をかけてセックス大好き淫亂ビッチにしてみた', 'car-1901041'],['[3Dエロアニメ] マル秘!!裏戦車道です!', 'car-1901042'],['[3Dエロアニメ] らきちぇり', 'car-1901043'],['ドSなマイナ會長サマがMノートに支配されました。 ～ドMに與する憧憬 do S', 'car-1901044'],['プリーズ・○○○・ミー！ ～九條さくら ピーを…ピーに…ピーして下さい◆ 編～', 'car-1901045'],['婊子醫生趁機和病人ooxx內射中出', 'car-1901046'],['[3Dエロアニメ] ロリアイドルまりか～処女膜見せつけお尻セックス～', 'car-1901071'],['[3Dエロアニメ] 古書堂奇譚 鷺沢無情[便衣版]', 'car-1901072'],['[3Dエロアニメ] 淫中毒者キモオヤジ', 'car-1901073'],['3D同人輯《生死格鬥》女天狗', 'car-1901074'],['H卡通-3D 草莓100%東城綾', 'car-1901075'],['爆乳美女的性飢渴', 'car-1901076'],['おっとりサキュバスちゃん 性春白書', 'car-1901081'],['妄想的電梯小姐-01', 'car-1901082'],['美少女ウルトラヒロイン3', 'car-1901083'],['巨乳姐姐的叫床聲真迷人', 'car-1901084'],['爆乳妹妹又媚的足交性愛', 'car-1901085'],['鷺沢 非情 痴漢に喘ぐ文學少女 無殘エロ墜ち肉便器', 'car-1901086']];

		$countTitles = 0;
		foreach($datas as $key => $data)  
		{
			foreach($avkeyTitles as $avkeyTitle)  
			{
				if( $data['avkey'] == $avkeyTitle[1] )
				{
					$datas[$key]['title'] = $avkeyTitle[0];
					$datas[$key]['content'] = $avkeyTitle[0];
					$countTitles++;
				}
			}
		}

		//dd($datas);
		//As = [ ['avkey', duration, title, content, views, video_source, video_type, cover_index, tags] ]
		$tempStr1 = "[['";
		//$tempStr1 = "['";   //只撈avkey

		foreach($datas as $data)  
		{
			$str_time = $data['duration'];
			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
			//$tempStr1 = $tempStr1 . $data['avkey'] . "', " . $time_seconds . "], ['";
			//$tempStr1 = $tempStr1 . $data['avkey'] . ", ";   //只撈avkey
			/*
			//As = [ ['avkey', duration, title, content, views, video_source, video_type, cover_index, tags] ]
			$tempStr1 = $tempStr1 . $data['avkey'] . "', " 
						. $time_seconds . ", '" 
						. $data['title'] . "', '" 
						. $data['content'] . "', "
						. $data['views'] .", " 
						. $data['video_source'] .", " 
						. $data['video_type'] .", " 
						. $data['cover_index'] .", '" 
						. $data['tags'] 
						. "'], ['";
			*/
			//As = [ ['avkey', duration, title, content, video_source, video_type] ]
			$tempStr1 = $tempStr1 . $data['avkey'] . "', " 
						. $time_seconds . ", '" 
						. $data['title'] . "', '" 
						. $data['content'] . "', "
						. $data['video_source'] .", " 
						. $data['video_type']
						. "], ['";


		}
		//dd('333');

		dd($tempStr1);	
	
	}

	// get video infomation for database
	//  20190425
    public function getVideoInfo()
    {
		$datas = \App\GetVideoInfo::orderBy('id','desc')
			->where('user_id',7)
			->where('method','DELETE')
			->get()
			->pluck('path')
			->toArray();
		$tempStr = '';

		foreach($datas as $data)
		{
			$tempStr = $tempStr . str_replace("admin/AvVideo/","",$data) . ",";
		}

		$tempStr = substr($tempStr,0,-1);
		$tempStr = explode(",",$tempStr);
		$datas = \App\AvVideo::orderBy('id','desc')
			->where('video_source', 2)
			->where('id', '=', 19401)
			->get()
			->toArray();

		//自拍
		$avkeyTitles = [['Littlesubgirl 迪卡侬潮喷门事件', 'self-1808151'],['Littlesubgirl 公开手淫', 'self-1808152'],['Littlesubgirl-喷在脸上', 'self-1808161'],['Littlesubgirl-情色实况主', 'self-1808162'],['Schoolgirl Squirting as She Rides Client-720pTrim', 'self-1808171'],['Littlesubgirl-淋浴中肛門自衛', 'self-1808172'],['Littlesubgirl-网红主播双洞自慰', 'self-1808201'],['Littlesubgirl-性感的网红主播在酒店手淫', 'self-1808202'],['高潮痙攣的情色實況主 Littlesubgirl Tia 高潮淫水噴出抽搐不斷 ', 'self-1808211'],['民視白嘉琪_Trim', 'self-1808212'],['輔仁大學 學生妹 口爆 肛交 奶泡', 'self-1808221'],['Teen Loves to Suck Cock and Swallow Cum - Littlesubgirl - P', 'self-1808222'],['長髮身材模特兒級美女和男友在酒店愛愛被偷偷拍下 ', 'self-1808231'],['Hot Asian with Perky Tits gets Fucked Hard in her Ass - Lit', 'self-1808232'],['變性人妖秀，巨根人妖直播和白虎正妹女友，人妖上女人無套做愛', 'self-1808241'],['小黑妹挺肉棍差到小西瓜', 'self-1808242'],['日本人妖性奴調教', 'self-1808243'],['日本人妖插穴穿著水手服制服教室做愛', 'self-1808244'],['台湾正妹人妖口交手机自拍短片流出', 'self-1808245'],['巨根人妖操人妖做愛插菊穴', 'self-1808246'],['那個E乃妹子怎麼有根吊', 'self-1808247'],['超大屌人妖打手槍', 'self-1808248'],['碗公奶摩鐵自拍泰國人妖打手槍', 'self-1808249'],['華航空姐3', 'self-18082410'],['華航空姐', 'self-18082411'],['華航空姐2', 'self-18082412'],['21岁的气质小主播床上情趣内衣给客官们展示美穴', 'self-1808281'],['人瘦却有极品巨乳的女主播很是诱惑 ', 'self-1808282'],['大膽短髮妹竟在知名大賣場露出自拍 ', 'self-1808283'],['女大生圖書館自拍17分情色片網路瘋傳', 'self-1808284'],['小伙开房肏了个美女，竟然还偷拍人家', 'self-1808285'],['小颖黑丝，一边舔脚一边足交', 'self-1808286'],['太陽花女神 Sun Flower from Taiwan', 'self-1808287'],['手机直播非常骚的主播SM 丝袜', 'self-1808288'],['台灣台北真實外送茶自拍', 'self-1808289'],['台灣某護理師淫蕩自慰', 'self-18082810'],['台灣實況主外流1', 'self-18082811'],['台灣實況主外流2', 'self-18082812'],['台灣實況主外流3', 'self-18082813'],['台灣豪放美眉與外國男友的淫亂生活', 'self-18082814'],['把漂亮大學生女網友騙到出租屋迷倒慢慢玩', 'self-18082815'],['足+自慰', 'self-18082816'],['乖巧女友足交', 'self-18082817'],['兩女足交', 'self-18082818'],['夜店廁所側錄 ', 'self-18082819'],['河南某高三情侶打炮視訊流出 ', 'self-18082820'],['某大學校花又被偷拍外流 ', 'self-18082821'],['紅衣美女主播自慰大秀', 'self-18082822'],['真實偷拍網吧包間情侶打炮', 'self-18082823'],['國內屌絲男湊錢開房玩3P', 'self-18082824'],['週末和極品白嫩情人賓館房間玩足交後再做愛 ', 'self-18082825'],['疑似蛇姬林采緹不雅短片曝光', 'self-18082826'],['台灣車模含著肉棒淫蕩口交短片流出', 'self-1809031'],['黑道綁架凌辱調教，男友被綁在旁，女友被多P強制口交吞精喝尿', 'self-1809032'],['北京人妖約炮男屌人妖肛交性愛自拍人妖做愛視頻', 'self-1809033'],['台上潮吹表演，順便幫台下觀眾洗臉', 'self-1809034'],['台灣大學情侶自拍在小公寓做愛，筆電維修外流', 'self-1809035'],['台灣出差男和超正的D奶美女約炮做愛高清針孔偷拍', 'self-1809036'],['中國內地絲足會所按摩約炮遇到超正美女技師，美腳正妹足交', 'self-1809037'],['房東偷拍台灣女學生自慰不小心照到臉', 'self-1809038'],['迷姦自拍無套做愛中出顏射一臉', 'self-1809039'],['19歲援交妹的女同訓練', 'self-1809051'],['日本美女Coser角色扮演貓耳娘兔女郎做愛視頻', 'self-1809052'],['台灣H奶巨乳人妻裸體圍裙摳穴自慰直播賺奶粉錢', 'self-1809053'],['台灣富少淫魔李宗瑞迷昏吳姓女星Maggie自拍性愛短片艷照門淫照流出', 'self-1809054'],['按摩棒漏電', 'self-1809055'],['美女和男友開房時害羞用被子蓋著不讓操', 'self-1809056'],['停路邊玩車震', 'self-1809057'],['帶極品大奶正妹到五星級摩鐵狂幹1', 'self-1809058'],['淫蕩飢渴人妻', 'self-1809059'],['漂亮美女和男友開房', 'self-18090522'],['閨蜜剛走男的就迫不及待把她按倒啪啪', 'self-18090510'],['慾罷不能，誤食媚藥潮吹直播自慰淫水濕身秀', 'self-18090511'],['在KTV唱歌唱到一半干起来', 'self-1809061'],['跟大叔在路邊草地上野戰', 'self-1809062'],['女神嘗試瘋狂3P', 'self-1809063'],['可愛微乳妹跟男友性愛自拍', 'self-1809064'],['台灣正妹主播小林志玲～直播脫衣秀舞加自慰視頻', 'self-1809065'],['台灣美女喝醉後被同事撿屍帶去摩鐵開房抓奶揉乳眠姦手機性愛自拍影片流出', 'self-1809066'],['台灣淫蕩人妻愛撫自拍還被摳穴指姦，用黑絲足交無套做愛自拍影片流出', 'self-1809067'],['台灣視訊妹自慰愛撫湧泉蜜穴潮吹愛液一洩千里滿滿流出', 'self-1809068'],['台灣觀光客和泰國人妖做愛自拍短片，人妖肛交無套', 'self-1809069'],['平面模特兒真實迷姦自拍影片流出，醉後眠姦無套做愛', 'self-18090610'],['操爆女神級身材D奶女炮友', 'self-18090611'],['雙飛兩個大奶泳裝模特', 'self-18090612'],['在IKEA家具行里公然自慰', 'self-1809071'],['上海美女（伊人馨儿）和帥哥炮友賓館', 'self-1809072'],['大奶交換學生口爆吞精', 'self-1809073'],['大眼主播自慰给观众看', 'self-1809074'],['小情侣宾馆床上大战到浴缸', 'self-1809075'],['主播來大姨媽了也不休息野外直播', 'self-1809076'],['性愛實況演出，無套做愛深喉口交影片', 'self-1809077'],['某知名女團成員換臉按摩棒自慰直播影片', 'self-1809078'],['坐檯小姐high過頭包廂現場就x了起來 ', 'self-1809079'],['被人體固定的美女性奴調教到連續強制高潮視頻', 'self-18090710'],['和真人矽膠性愛娃娃無套做愛尻射', 'self-18090711'],['性感漂亮的售樓小姐', 'self-18090712'],['泰國絲襪人妖自慰賣萌同時幫猛男口交打手槍，再被男屌人妖肛交插穴', 'self-18090713'],['女性奴地獄監禁窒息高潮愉虐性愛，SM綑綁，滴蠟虐待乳頭穿刺', 'self-18090714'],['多P暴力輪姦強暴影片，顏射淋尿凌辱調教美女性奴', 'self-18090715'],['飢渴難耐的妻子帶丈夫上賓館培養情趣', 'self-18090716'],['週末就是要在家爆操美乳絲襪女友', 'self-18090717'],['网红穿开裆黑丝在椅子上自摸', 'self-18090718'],['路上塞車等不急，約炮交友來到公廁無套做愛自拍流出', 'self-18090719'],['女性奴被男主人懸吊調教強制人體固定戶外露出', 'self-18090720'],['操爆大奶美乳美尻淫蕩妹子', 'self-18090721'],['真人娃娃乳交打奶炮自拍乳射影片', 'self-18090728'],['女大學生忘記關直播鏡頭，宿舍換衣被網友側拍偷拍看光全都露', 'self-1809111'],['中國名模李ｘ艷照門性愛視頻', 'self-1809112'],['台灣公廁偷拍女孩小便', 'self-1809113'],['巨乳大奶妹乳交性愛自拍視頻，深溝乳射超爽Der', 'self-1809114'],['打手槍技術一流的色情按摩店刺青女技師幫男客口交性愛服務', 'self-1809115'],['坐檯小姐high過頭包廂現場就干了起來', 'self-1809116'],['身材超正的美乳辣妹浴室自慰哼歌冲凉洗澡直播', 'self-1809117'],['乳交不稀奇乳头交厉害吧！', 'self-1809118'],['兔女郎美女女同志直播拳交視訊側拍影片', 'self-1809119'],['奇葩大乳头妹大到可以塞鸡鸡', 'self-18091110'],['拉丁正妹潮吹直播用強力跳蛋自慰到高潮迭起，極品渾圓美臀視訊，小穴被撐開像在下蛋', 'self-18091111'],['精液面膜幫女客敷臉，同時其他6個男技師一起幫忙全身推油乳房按摩', 'self-18091112'],['柳州莫菁門～艷照視頻第1部', 'self-18091113'],['柳州莫菁門～艷照視頻第2部', 'self-18091114'],['台灣正妹穿著透視水手服直播和男友揉乳調情', 'self-18091115'],['恐怖AV電影 鬼修女 插入(英文字幕) ', 'self-18091116'],['秘密相機拍攝廁所自慰 ', 'self-18091117'],['碗公奶正妹情趣跳蛋絕頂高潮痙攣直播', 'self-18091118'],['驚人的肛容量！視訊正妹直播從肛門取出50公分的雙頭龍按摩棒', 'self-18091119'],['Littlesubgirl-知名網紅主播想被大雞巴填滿', 'self-1809121'],['Littlesubgirl-知名網紅主播用粗大性玩具玩弄肛門', 'self-1809122'],['Littlesubgirl-知名網紅主播自慰到狂抽蓄', 'self-1809123'],['Littlesubgirl-知名網紅主播自慰將噴出的淫水舔乾淨', 'self-1809124'],['Littlesubgirl-無毛知名網紅主播午休時間到廁所用假陽具自慰', 'self-1809125'],['Littlesubgirl-知名網紅主播穿著制服吃著雞雞', 'self-1809126'],['Littlesubgirl-知名網紅主播想被大雞巴填滿', 'self-1809127'],['Littlesubgirl-知名網紅主播替朋友口交實況直播', 'self-1809128'],['白嫩美乳港女幹砲自拍', 'self-1809129'],['大波靚模性愛影片外流 一直不停大喊:搞我', 'self-18091210'],['與白虎女友上旅館口交足交', 'self-18091211'],['中國高清自拍性愛片 美尻口交', 'self-18091212'],['公司廁所盜拍女職員自慰影片', 'self-18091213'],['夫妻自拍幹炮影片人妻瘋狂抽蓄絕頂高潮停不了', 'self-18091214'],['水淚白滑乳韓妹子視訊送上身裸福利', 'self-18091215'],['台灣妹子：你不要拍我會太興奮', 'self-18091216'],['台灣招妓偷拍下來分享', 'self-18091217'],['巨乳牛乳西瓜乳女護士', 'self-18091218'],['本土換妻俱樂部私人聚會群交派對6P約炮人妻性愛自拍影片流出', 'self-18091219'],['老闆娘與男員工偷情閉路電視影片流出', 'self-18091220'],['自拍誘姦少女的戰績', 'self-18091221'],['巨屌巨乳雙性人女友自拍口交口爆玩菊花1', 'self-18091222'],['巨屌巨乳雙性人與女友自拍口交口爆玩菊花頭戴內褲2', 'self-18091223'],['妹子喜歡在火車上自慰', 'self-18091224'],['穿著性感吊襪帶和白色透膚膝上襪的巨根人妖打手槍自拍', 'self-18091225'],['美女自拍口交炮友', 'self-18091226'],['強姦遊戲性愛自拍，女友求饒 絲襪被撕破綑綁在椅子上性侵凌辱', 'self-18091227'],['情侶感覺來了直接在馬路上幹了起來', 'self-18091228'],['最佳美臀美尻女友干炮自拍', 'self-1809131'],['超淫​​荡骚货大喊:我被插到受不了', 'self-1809132'],['微信约炮内衣店妹子', 'self-1809133'],['痴汉偷入女神家放镜头偷拍到刺激自慰情景', 'self-1809134'],['禽兽父亲自拍猛干女儿粉嫩肉穴', 'self-1809135'],['疑似我是歌手-张x晨 性爱影片外流', 'self-1809136'],['KTV里躺沙发上被三男人轮流干有说有笑还有自拍录像', 'self-1809137'],['KTV醉酒干炮旁人唱歌我們做爱自拍', 'self-1809138'],['可爱美女网红Cosplay兔女郎爆乳自慰卖萌', 'self-1809139'],['台湾视讯妹自慰直播潮吹洪水成灾', 'self-18091310'],['女大生痴女开视讯自玩黑鲍到潮吹', 'self-18091311'],['巨乳为求名利视讯秀巨乳', 'self-18091312'],['幼齿大奶高丽视讯妹', 'self-18091313'],['网红主播直播自慰到绝顶高潮晕过去', 'self-18091314'],['西安妹子人嫩内骚红衣直播假阳具M字腿深插', 'self-18091315'],['乖乖女学生私下很淫视讯给男同事看自慰过程', 'self-18091316'],['两极品软妹子 家中直播cosplay警察犯中制服诱惑互相爱抚', 'self-18091317'],['青瓜女自慰用青瓜至潮吹大喊:好爽阿', 'self-18091318'],['美女女主播视讯整个情趣玩具自慰至潮吹高潮', 'self-18091319'],['浴室玩水自慰爱抚给你看', 'self-18091320'],['淫妹子直播用青瓜自慰还不够还要按摩棒相助', 'self-18091321'],['这个屁眼被操得太多太大', 'self-18091322'],['视讯女主播用酒瓶跟拳交塞淫穴自慰', 'self-18091323'],['痴女视讯自慰淫水爱液满出来', 'self-18091324'],['大学情侣爱爱自拍外流', 'self-1809141'],['中国上海嫩模李X完美巨乳自拍影片流出', 'self-1809142'],['可爱人妖肛交口爆 不一样的淫样', 'self-1809143'],['美乳纤细肉体被红色SM绳绳缚捆绑 美丽又性感', 'self-1809144'],['可爱少女电车强暴', 'self-1809145'],['台湾美女女孩手机自拍性爱片流出', 'self-1809146'],['外国狂野寻欢洞', 'self-1809147'],['哪个美穴看起来好干就插哪个洞', 'self-1809148'],['巨乳受虐癖女双乳逾被凌辱逾兴奋', 'self-1809149'],['老婆不让拍只好偷拍 美人妻身材好又大胸', 'self-18091410'],['与男友讲电话边跟小王干炮', 'self-18091411'],['两美女女同志互相口交肉鲍磨豆腐', 'self-18091412'],['妹子半夜去便利店被变态流浪汉跟店员当场强奸3P ', 'self-18091413'],['妹妹自慰 爽到喷水 ', 'self-18091414'],['美女被捆绑 一群人颜射', 'self-18091415'],['国中妹早上很渴很想要', 'self-18091416'],['国产3p-性爱场面真是太乱', 'self-18091417'],['寂寞人妻自己插到喷水', 'self-18091418'],['SM凌虐上司', 'self-18091419'],['情侣高清自拍干炮 女友等被插', 'self-18091420'],['淫妹肉棒不够吃要拳头插入黑鲍鱼', 'self-18091421'],['嫩小穴鲍鱼假阳具自慰淫水流满桌', 'self-18091422'],['网拍女模脸书放上性爱影片奶子很大', 'self-18091423'],['与好友一起视讯干炮磨豆腐', 'self-18091424'],['豪放情侣视讯直播69口交', 'self-18091425'],['好姊妹一起直播脱衣网秀当网上红人', 'self-18091426'],['18歲高中萌妹裸拍抖音被肉搜！ ：這發育真狂', 'self-1809171'],['17直播F奶网红女主播浴室揉乳手机侧拍流出影片', 'self-1809172'],['36D豪乳95后嫩妞 激烈快速抽插 爆射美乳', 'self-1809173'],['大战很多水的外配人妻, 水如雨下', 'self-1809174'],['Claire露出大奶让肥宅摄影师玩弄', 'self-1809175'],['人妖健身房3P性爱趴，美人妖做爱互相肛交口交打手枪', 'self-1809176'],['超美巨根人妖与女朋友性爱', 'self-1809177'],['用超级巨屌教训路边搭讪的骚包', 'self-1809178'],['这个美人有根巨屌 美人妖与女朋友干炮', 'self-1809179'],['三个骚人妻相约一起自拍自慰一起爽 吟声不绝传四层', 'self-18091710'],['口爆中国女友少女时代无码自拍啪啪啪', 'self-18091711'],['自慰潮催到喷水抽蓄', 'self-18091712'],['玩弄中号罩杯巨乳人妻的大奶子', 'self-18091713'],['美女爱吃大屌喷射在嘴里 颜射吃精', 'self-18091714'],['情侣在卖场更衣室干了起来', 'self-18091715'],['网红主播在大卖场用家具跟酒瓶自慰', 'self-18091716'],['网红主播高尺度户外自慰露出', 'self-18091717'],['口交然后吞精', 'self-1809181'],['女友吃屌舔蛋技术超好 马上射出', 'self-1809182'],['女星X幂疑似遭迷奸影片流出 哭喊我老公呢', 'self-1809183'],['不小心喷了出来', 'self-1809184'],['中国人在出租屋内小姑娘一直喊我要', 'self-1809185'],['中国人超大奶子美女诱惑自拍', 'self-1809186'],['中国大奶女友 晃动的好兴奋', 'self-1809187'],['写真拍了一半摄影师忍不住上了女模特，呻吟声超级骚', 'self-1809188'],['用肩颈按摩器自慰到喷水潮吹', 'self-1809189'],['网吧勾引色诱保安到值班室啪啪', 'self-18091810'],['网络红人严佳丽超大尺度露三点激情自慰视频', 'self-18091811'],['老师用淫语教大家新知识', 'self-18091812'],['我的女友身材好腰力又好', 'self-18091813'],['我的闺蜜有根大屌', 'self-18091814'],['我憋不住了, 不行, 你快射出来 ! (国语)', 'self-18091815'],['哥哥给初中生妹妹破处', 'self-18091816'],['射在女友的脸上', 'self-18091817'],['胸部很大的波波妹 流出视频', 'self-18091818'],['巨根18岁变性人妖直播自慰实况给网友们看', 'self-18091819'],['骚女大喊我要吃, 我要吃 !!', 'self-18091820'],['人妻偷情突然老公打来 还按扩音边干炮', 'self-1809191'],['女大学生被骗到酒店用内裤塞嘴强干 直挥泪求饶', 'self-1809192'],['小不点把我舔到狂喷水', 'self-1809193'],['中国人西安茄子女自拍露脸高潮喷水', 'self-1809194'],['少妇欠下巨债被迫接客自拍还债泪流满脸', 'self-1809195'],['豆豆是这样玩出水的', 'self-1809196'],['和朋友一起干饥渴的大奶富姐，轮番上阵仍不满足实在骚货', 'self-1809197'],['拍下女友大奶晃动的骚样', 'self-1809198'],['这个叫床声听了都硬了', 'self-1809199'],['哥哥对妹妹伸出狼爪', 'self-18091910'],['粉嫩鲍鱼潮吹 水声跟淫声好有成就感', 'self-18091911'],['被哥哥强插', 'self-18091912'],['这妹子洞大到头都塞得进去 还大量潮吹', 'self-18091913'],['超可爱人妖互打手枪射出 叫声好骚', 'self-18091914'],['黄瓜肏出汁了', 'self-18091915'],['跟老公好朋友一起干炮 还大喊老公快撞我', 'self-18091916'],['跟我的闺蜜一起互磨豆腐', 'self-18091917'],['网红主播勾引送外卖的小哥', 'self-18091918'],['荡妇一边和老公通电话 一边和情人做爱(精彩国语对话)', 'self-18091919'],['帮人妖女友口交', 'self-18091920'],['色诱送货员', 'self-1809201'],['这水量隔着裤子都能喷射出来', 'self-1809202'],['一家人一起运动好幸福啊', 'self-1809203'],['中国人叫声刺激，白浆流了这么多', 'self-1809204'],['中国人骚女沙发上做爱喷白浆', 'self-1809205'],['分手报复性爱影片流出', 'self-1809206'],['外国男子的变态性癖好', 'self-1809207'],['白花花软嫩奶一直不停弹跳', 'self-1809208'],['白虎酥胸粉红奶头忍不住吸允', 'self-1809209'],['网红李雅完美身材国语对白', 'self-18092010'],['找了对夫妻玩3P，对方老婆奶子好大啊！白花花的晃的眼晕！', 'self-18092011'],['性爱派对大乱交', 'self-18092012'],['爸爸的性教育 潮吹水柱喷上天', 'self-18092013'],['某大学超人气爆乳拉拉队长和帅哥激情啪啪戴着眼镜样子很淫骚', 'self-18092014'],['某大学校花4分30秒遭报复影片流出', 'self-18092015'],['美女KTV唱歌喝多了被带到酒店内裤塞嘴绑住手脚玩弄', 'self-18092016'],['美颜欲情女自慰痉挛盗摄', 'self-18092017'],['哦, 宝贝, 不要了 ! (普通话)', 'self-18092018'],['无毛白虎拿苦瓜自慰', 'self-18092019'],['无码高潮淫叫爽到喷尿，淫水直流呻吟不止', 'self-18092020'],['黑丝美腿女主播约发廊小伙到野外啪啪啪刺激大声淫叫也没事', 'self-18092021'],['隔壁邻居小孩真调皮', 'self-18092022'],['韩国不良少男少女群交自拍4P干炮', 'self-18092023'],['小妹妹半夜健忘自尊粗二手便宜的安慰', 'self-1809211'],['中国骚主播拿阴道摄影机探索内部', 'self-1809212'],['少女玩弄自己无毛白嫩小穴制服网袜珍珠内', 'self-1809213'],['主管趁小妹在接电话偷偷舔无毛鲍鱼', 'self-1809214'],['好可口的粉色鲍鱼', 'self-1809215'],['网红主播莫妮卡粉逼大尺度自拍', 'self-1809216'],['自慰粉色的奶跟鲍鱼', 'self-1809217'],['尿尿水柱刺激阴蒂', 'self-1809218'],['知名网红羽沫 在高铁台中站自拍', 'self-1809219'],['穿网袜的妹子自慰到失神', 'self-18092110'],['美女主播最新插肛自慰视频', 'self-18092111'],['真实迷奸 把朋友灌醉带回住宿干她', 'self-18092112'],['粉嫩奶头妹子自拍传给我看要我帮他灭火', 'self-18092113'],['被两根巨大的屌欺负', 'self-18092114'],['吸允自己奶头自慰', 'self-18092115'],['富商花高价钱找2个高中妹子破处，干的妹子直喊疼', 'self-18092116'],['无毛小萌妹不甘寂寞，只好流着眼泪自慰', 'self-18092117'],['传说假戏真做的电影片段', 'self-18092118'],['网络红人cc凌溪宠儿和男友大尺度自拍作品', 'self-18092119'],['潮吹喷射天空 抽蓄无法停止', 'self-18092120'],['人妻被拖到工廠輪姦', 'self-1809251'],['女主播播報新聞邊被抽插', 'self-1809252'],['中国饥渴小少妇家中自慰用床栏杆插逼', 'self-1809253'],['中国体操少女性爱解锁不同姿势', 'self-1809254'],['台灣半套店朋友偷拍小姐口交', 'self-1809255'],['奶子超美的小表妹睡觉的时候被操', 'self-1809256'],['吐血推荐身材爆炸的极品小萝莉', 'self-1809257'],['在圖書館與大叔做愛', 'self-1809258'],['車模被人狂操', 'self-1809259'],['姊姊用大奶誘惑我', 'self-18092510'],['派對上喝醉被起鬨做愛', 'self-18092511'],['宾馆迷奸女同事和她超漂亮女儿-无套爽爆', 'self-18092512'],['跑進大嫂房間做愛 哥哥還躺在旁邊', 'self-18092513'],['中國模特外拍到一半與工作人員搞上', 'self-1809261'],['把大奶女同事灌醉後強姦', 'self-1809262'],['性感美女邀请网红男到家中直播差点被操哭', 'self-1809263'],['飛機上特別的團體性交服務', 'self-1809264'],['浦东新区父女乱伦事件', 'self-1809265'],['偷拍高中小情侶野外愛撫', 'self-1809266'],['被安装在酒店的摄像头偷拍了还在忘情的干', 'self-1809267'],['綜藝節目 插看看哪個是媽媽', 'self-1809268'],['灌入迷药迷奸', 'self-1809269'],['露脸女神空姐人妻Helen后入水多叫声销魂', 'self-18092610'],['口交兼乳交置身天堂', 'self-1809271'],['完美身材自慰粉穴', 'self-1809272'],['幸运的姐夫和老婆一起肏了小姨子，对白好淫荡', 'self-1809273'],['玩遊戲落水 被救起露點曝光', 'self-1809274'],['某导演潜规则女演员', 'self-1809275'],['粉色大奶網紅主播自慰', 'self-1809276'],['偷偷在花園自慰用淫水來澆花', 'self-1809277'],['專業秒射夾奶乳交 ', 'self-1809278'],['這大量潮吹噴水量太誇張了', 'self-1809279'],['獸父叫女兒幫他打手槍', 'self-18092710'],['女友用又白又軟的胸部幫我乳交', 'self-1809281'],['大型換妻俱樂部', 'self-1809282'],['火辣美女主播挑逗外卖小哥秒硬-中间还给操喷了', 'self-1809283'],['史上最大性愛派對', 'self-1809284'],['好喜歡吃男友的新鮮精液', 'self-1809285'],['自己乳交射在自己巨大胸上', 'self-1809286'],['男友粗暴的射精在我臉上 叫聲讓我好興奮', 'self-1809287'],['試插看看哪個騷洞最好用', 'self-1809288'],['臺灣能仁家商高二女生和導師私下援交', 'self-1809289'],['幫男友口爆 男友教聲好有成就感', 'self-18092810'],['幫男友乳交秒噴射在我大奶上', 'self-18092811'],['中国人巨乳人妻被情人使劲玩奶子', 'self-1810011'],['在ktv包厢爆操女神 叫聲好浪', 'self-1810012'],['在懸崖吊鋼絲性愛欣賞美景', 'self-1810013'],['我的女友邊跟我做愛邊自己打手槍', 'self-1810014'],['姊姊在我身上忘情扭動', 'self-1810015'],['家族流傳的成年禮', 'self-1810016'],['真實 姊姊在旁邊睡覺我誘惑姐夫', 'self-1810017'],['做愛技術大賽開始', 'self-1810018'],['單身派對 姐妹們共吸一隻屌', 'self-1810019'],['辦公室午休狂歡時間', 'self-18100111'],['霸凌-大陸國中生強模同學咪咪還攝影', 'self-18100112'],['M罩杯的姊姊自慰', 'self-1810021'],['女友的大奶子比我頭還大', 'self-1810022'],['在海邊被女友乳交夾到噴出來', 'self-1810023'],['我的女友的屌比我還大隻', 'self-1810026'],['乳交大合集 各種乳交', 'self-1810027'],['兩位美女巨屌人妖互相解套', 'self-1810028'],['迷奸3个粉木耳,还内射', 'self-1810029'],['迷奸朋友的小女儿', 'self-18100210'],['被姊姊夾的好爽阿 忍不住噴出', 'self-18100211'],['已婚女秘书一边做一边和闺蜜讲电话', 'self-1810031'],['网络红人女神思瑞和土豪啪啪神情销魂大叫老公不要停', 'self-1810034'],['肛交内射超水嫩尤物女友-大奶被干的晃来晃去', 'self-1810035'],['乳牛巨根人妖打手槍噴射到天空', 'self-1810036'],['超巨根人妖插入自己肛門', 'self-1810037'],['想多學點做愛技小回去給老公爽', 'self-1810038'],['顏射在L奶女友臉上-4', 'self-1810039'],['讓我的人妖女友爽到飛上天', 'self-18100310'],['小小年纪就偷尝禁果還自拍影片', 'self-1810041'],['中国人昏暗的酒店爆操大奶美女', 'self-1810042'],['综艺节目 插看看哪个是親身妹妹', 'self-1810043'],['侄女VS叔叔', 'self-1810044'],['迷姦夜店妹子帶回家內射自拍', 'self-1810045'],['清纯女友寝室做爱 隔壁有同学不敢大叫', 'self-1810046'],['被同學霸凌綑綁強姦用湯匙打我鮑魚', 'self-1810047'],['露脸可爱大二眼镜妹子说喜欢小一点的鸡', 'self-1810048'],['女友說我的肉棒讓她很滿足', 'self-1810051'],['在海边玩起天体性爱派对', 'self-1810052'],['美女高呼：我不要了,你的鸡巴要操死我了', 'self-1810054'],['剛虧到的妹子直接約我在海邊運動', 'self-1810055'],['清純學生女在家自慰影片留出', 'self-1810056'],['想讓外面的人看見我們的激情', 'self-1810057'],['極致完美身材邊聽音樂邊撫摸自己', 'self-1810058'],['魔鬼的身材天使臉蛋', 'self-1810059'],['口渴了將老公精液一飲而盡', 'self-1810091'],['用妹妹的美腳幫哥哥足交', 'self-1810092'],['找閨蜜一起讓我男友足交到射', 'self-1810093'],['姐姐足交技術超好 射的我滿身都是', 'self-1810094'],['青春期發騷的小女孩', 'self-1810095'],['美腳足交忍不住抓起來狂插', 'self-1810096'],['幫姊姊做觸診胸部檢查', 'self-1810097'],['邊做愛我的奶水就一邊噴出', 'self-1810098'],['大奶妹子怎麼有根屌', 'self-1810099'],['中国白天野外干小炮友一直捂着嘴怕喊出声', 'self-18100910'],['父亲和儿子与女佣人发生性关系', 'self-18100911'],['女友穿著帆布鞋幫我打飛機', 'self-18100912'],['淘寶小嫩模完美約會', 'self-18100913'],['喝完酒回来小姨子以为我喝醉了拿出鸡巴舔硬后自己坐上去操', 'self-18100914'],['最喜歡幫人妖女友把精液吸出來', 'self-18100915'],['骚货在公厕被3P，还饥渴的舔开小便池', 'self-18100916'],['騷模酒店誘惑攝影師', 'self-18100917'],['上班日火辣女友拍誘惑視頻給我 硬一整天', 'self-1810111'],['中国上海女模李X在性愛影片流出', 'self-1810112'],['华人区总裁女上司为解决性欲找来男下属操', 'self-1810113'],['后入和女神疯狂做爱 叫声诱人', 'self-1810114'],['老二勃起比男人還粗壯的大屌人妖口交巨根肉棒', 'self-1810115'],['国内夫妻宾馆玩3p 肛交全程普通话', 'self-1810116'],['国产真实迷姦妹子還自拍影片', 'self-1810117'],['结婚第一年银行美女爱妻被我调教成騷貨', 'self-1810118'],['真实迷奸-某地小有名气的极品平面模特被男友灌醉后让朋友啪啪', 'self-1810119'],['中国人小情侣在城中村出租屋做爱自拍外流', 'self-1810121'],['中国人高中生早恋稚嫩性交', 'self-1810122'],['巨乳媽媽教導我性交姿勢', 'self-1810123'],['我與女友戀足癖的性生活', 'self-1810124'],['狂操上海外围女李雅 操的她大奶子乱晃', 'self-1810125'],['富二代豪宅双飞两个网络援交大学生', 'self-1810126'],['黑丝美女肛交轮插 自拍影片流出', 'self-1810127'],['與巨大黑屌性交', 'self-1810128'],['鄰居姐姐拍了誘惑視頻給我 叫我今晚去她家', 'self-1810129'],['幫鄰居打手槍射在我的鞋子裡潤滑', 'self-18101210'],['97年大二情侣到宾馆开房打开摄像头自拍做爱', 'self-1810161'],['5000大洋找了2个还在上高中的美眉玩双飞、爽爆了', 'self-1810162'],['FB人氣嫩模私下的兼職..被客人故意外流', 'self-1810163'],['女同事唱歌喝醉 直接在ktv侵犯', 'self-1810164'],['女同事喝多了無意識被上', 'self-1810165'],['小情侶打炮自拍送修手機視頻外流', 'self-1810166'],['中国性饥渴人妻在家被邻居随意践踏', 'self-1810167'],['中國上海嫩模李雅 賞巴掌真興奮', 'self-1810168'],['中國某95年院校系花玩3P', 'self-1810169'],['正看A片时女友闺蜜突然进来了,只好强行爆操', 'self-18101610'],['国产性爱party，普通话对白，场面太淫乱', 'self-18101611'],['穿黑絲女僕偷情 大喊射我裡面', 'self-18101612'],['瘋狂插入抖動的美臀', 'self-18101613'],['双飞气质老婆及其闺蜜开发半年成功', 'self-1810171'],['支开老公和小叔子厨房乱伦对白淫荡 ', 'self-1810172'],['本土真實與閨蜜交換夫妻自拍影片 ', 'self-1810173'],['本土酒店狂干小学女老师', 'self-1810174'],['在ktv包廂刺激女上位搖動腰力真好', 'self-1810175'],['酒店干美臀蜜汁亲表妹', 'self-1810176'],['温泉度假村，手機自拍享受90后嫩穴', 'self-1810177'],['媽媽現擠母乳餵我喝', 'self-1810178'],['下班回家路上強行迷姦學生妹帶回家', 'self-1810251'],['在姊姊的飲料裡下迷藥', 'self-1810252'],['在哥哥身上幹著意識不清的大嫂', 'self-1810253'],['穿著鞋幫老公足交 射好多', 'self-1810254'],['真實迷姦自拍還射在人家嘴裡', 'self-1810255'],['迷姦同事才發現他是粉色嫩鮑魚', 'self-1810256'],['迷姦指姦新來的年輕妹妹', 'self-1810257'],['高颜值黑丝高跟OL自拍酒店啪啪', 'self-1810258'],['停车场捡回酒醉妹!想不到是没被开发一字美鲍 ', 'self-1810259'],['船上的性愛派對', 'self-18102510'],['國產乱伦卡拉OK干妈妈:儿子我受不了', 'self-18102511'],['嫩模沒錢只好兼差約炮', 'self-18102512'],['漂亮小女生被下迷药', 'self-18102513'],['鞋交技術好棒 噴射在美腳上', 'self-18102514'],['不小心進錯廁所被三個痴漢玩弄', 'self-1810261'],['台灣人妻玩3P~一直喊幹我~', 'self-1810262'],['在教師辦公室大膽迷姦學生', 'self-1810263'],['我和女友与群内结识的情侣交换', 'self-1810264'],['国内夫妻宾馆玩3p 插雙洞全程普通话', 'self-1810265'],['带国航妻子参加群内交友聚会被单男精液灌满群P', 'self-1810266'],['國產20岁老婆首次3P前后洞被干潮吹', 'self-1810267'],['趁好兄弟不在 迷姦他的媳婦', 'self-1810268'],['幹爆大奶熟女人妻', 'self-1810269'],['爆操车展认识的模特,高潮大叫：,干我小骚B,好厉害,好爽!', 'self-18102610'],['幫閨蜜舔到潮吹', 'self-18102611'],['閨蜜狂挖我的G點讓我潮吹', 'self-18102612'],['與朋友的潮吹比賽', 'self-18102613'],['入侵鄰居家強迫性侵大奶騷樣人妻', 'self-1810291'],['巨根哥哥頂的好深', 'self-1810292'],['好喜歡被鄰居兩兄弟玩弄乳房', 'self-1810293'],['男友把我高舉綁在浴室肏我', 'self-1810294'],['乳房大到都垂到地上了', 'self-1810295'],['爸爸半夜溜進我的房間內射我..', 'self-1810296'],['爸爸檢查生給我的巨大乳房', 'self-1810297'],['看到孫女發育的那麼好忍不住獸性大發', 'self-1810298'],['媽媽看著我跟哥哥在餐桌上幹炮', 'self-1810299'],['幹死你這個大奶騷B', 'self-18102910'],['半夜溜進大嫂房間偷摸乳不小心吵醒她', 'self-1810301'],['狂摳學生妹的騷B讓她潮吹噴水抽蓄', 'self-1810303'],['搖一搖约到的白衣小仙女思妍', 'self-1810304'],['被男友的精液射滿整臉,各種顏射', 'self-1810305'],['與兄弟強姦美人妻 越呼救越興奮', 'self-1810306'],['學生妹被黑大屌幹到淫水四濺', 'self-1810307'],['褲子跟床單都沾滿女友的淫水', 'self-1810308'],['騷妻就是不吝嗇給大家看', 'self-1810309'],['小模的身材跟技術就是不一般', 'self-1810311'],['外送豪乳姐妹花', 'self-1810312'],['在妹妹水裡加迷藥半夜再進房強姦她', 'self-1810313'],['哥哥叫我進他的房間不准我跟爸爸媽媽說', 'self-1810314'],['哥哥說要教我只有大人能做的事情', 'self-1810315'],['红杏出墙的风骚性感人妻微信约網友见面诉苦', 'self-1810316'],['夜店結束將喝個爛醉的妹子帶回家', 'self-1810317'],['性感长腿美女穿着极其诱惑的黑色丝袜挑逗情人', 'self-1810318'],['高贵气质钢琴教师老婆首次3P', 'self-1810319'],['儿子好舒服啊对白淫荡的剧情母子乱伦大浴缸水中啪啪', 'self-1811011'],['在ktv包厢来一场激情大战爆操女神', 'self-1811012'],['江苏女神宁梓被下药迷奸视频流出-魔鬼身材绝世爆乳可惜了', 'self-1811013'],['网红美女被男粉丝强上', 'self-1811014'],['学生楼梯偷情同学下楼撞见-好尴尬', 'self-1811015'],['被學長強姦 都哭了還不放過我', 'self-1811016'],['颜值超高的女神级纯天然巨乳极品', 'self-1811017'],['邀同學來我家灌醉後迷姦', 'self-1811018'],['獸父誘姦兩個雙胞胎女兒', 'self-1811019'],['网美乳模特尔被摄影师用毛笔玩粉嫩小穴菊花', 'self-1811021'],['极品粉鮑高中網紅主播自拍影片流出', 'self-1811022'],['思妍小仙女喜歡用搖一搖認識大哥哥', 'self-1811023'],['國產真實迷姦-美女KTV喝多後把她帶到酒店2人輪流幹', 'self-1811024'],['喷血推荐-把暗恋已久的妹子灌醉带到酒店先刮毛再猛烈的抽插!', 'self-1811025'],['媽媽叫我去廚房幫他的忙..', 'self-1811026'],['舅舅搞外甥女还内射，妹子都被干哭了', 'self-1811027'],['鞋交恋足癖高跟鞋足交', 'self-1811028'],['今天醫生內診的方式好奇怪', 'self-1811051'],['台湾美女警察约炮自拍视频', 'self-1811052'],['华东理工洪X娟上海车展兼职被潜规则，', 'self-1811053'],['近期最淫乱的直播母女儿子3P乱伦节操碎满地看得出这妈妈年轻绝对是个大美女', 'self-1811055'],['某校附近微信約的19歲清純學生妹,第一次被約到賓館開房', 'self-1811056'],['绝色靓妹被人迷晕带到宾馆后醒了但全身无力只能装没醒任人奸淫', 'self-1811057'],['真实迷奸-长发美女喝多后被带到酒店玩弄后撕破丝袜狠狠抽插', 'self-1811058'],['真實迷奸眼镜大学生，正在来月经', 'self-1811059'],['國產微信搖到了一個騷妹子', 'self-18110510'],['水柔姐姐穿着性感情趣内衣和儿子乱伦', 'self-1811061'],['全程淫聲不斷的桑拿技师', 'self-1811062'],['网红女神鹿少女找邻居帮忙修理淋浴被侵犯', 'self-1811063'],['兩個擁有巨屌的人妖互相性愛高潮', 'self-1811064'],['和超漂亮的大学校花女神啪啪', 'self-1811065'],['南京大学清纯女友的另一面3p', 'self-1811066'],['漂亮人妖姐姐有根巨屌 打飛機射自己滿身', 'self-1811067'],['漂亮小女生被下迷药', 'self-1811068'],['綜藝節目 先讓媽媽高潮就贏了', 'self-1811069'],['一次迷奸3个閨蜜粉木耳,还内射', 'self-1811071'],['兩個巨屌人妖人妻互相插入至射精', 'self-1811072'],['国产真实母子乱伦', 'self-1811073'],['洗发水广告模特正在被潜规则中', 'self-1811074'],['浦东新区父女乱伦事件', 'self-1811075'],['迷姦心儀已久的女神 自拍留戀曝光', 'self-1811076'],['國產少妇公交站等老公被过路司机搭讪被下迷药拉到偏僻处车震', 'self-1811077'],['清纯学妹贪杯喝醉熟睡 被任意抚慰无套抽插內射', 'self-1811078'],['熟睡熟女被胖领导偷偷插入自拍視頻還内射', 'self-1811079'],['女神级纯天然巨乳被土豪操个不停', 'self-1811081'],['不小心在泳池內走光 挑起別人的慾望', 'self-1811082'],['去買早餐的路上看到一對情侶直接在路邊幹起來', 'self-1811083'],['同事女儿还在上高中 被我迷姦扒下花裤衩深入嫩穴', 'self-1811084'],['泳池內唯美刺激的性愛', 'self-1811085'],['彩美旬果無碼流出第一彈あやみ旬果無修正1', 'self-1811086'],['彩美旬果無碼流出第二彈あやみ旬果無修正2', 'self-1811087'],['淫欲的夜晚两对夫妻四个灵魂的换爱之夜', 'self-1811088'],['騎車騎到一半突然很想要 就直接用坐墊插入自己', 'self-1811089'],['儿子拉上窗帘 爆操继母 操的直叫老公快操死我吧超级爽', 'self-1811091'],['北京骚逼', 'self-1811092'],['半夜溜進哥哥房間眠姦性感大嫂', 'self-1811093'],['白虎少妇背着老公私会网友时不慎被迷姦被带回家边干边拍', 'self-1811094'],['在泳池內遇到一群渴望大屌的人妻們', 'self-1811095'],['良家下海卖淫按规则先被大哥干', 'self-1811097'],['和广东夫妻互换伴侣对方草两位夫人', 'self-1811098'],['逛生鮮超市直接用黃瓜插入自慰', 'self-1811099'],['獸父叫讀國小的女兒幫自己口交', 'self-18110910'],['大奶乳暈妹喝多了被朋友帶回家騎', 'self-1811121'],['母子亂倫 忍不住偷摸熟睡的媽媽', 'self-1811122'],['用身體還債 邊做邊哭泣', 'self-1811123'],['上海车模援交偷拍', 'self-1811124'],['美女KTV喝醉只能任人擺布摳她B', 'self-1811125'],['迷姦大奶妹子 被幹清醒後還用手摀住她嘴巴', 'self-1811126'],['迷姦補習班學生', 'self-1811127'],['被哥哥強插 一直大哭也沒用', 'self-1811128'],['溜進高中青春期妹妹房間', 'self-1811129'],['小伙迷奸后妈還將影片傳到微信公開', 'self-1811131'],['干爹给18岁女儿破处，逼毛都没有', 'self-1811132'],['对白淫荡有趣的母子对着化妆镜草妈妈射了好多', 'self-1811133'],['妈妈浓妆艳抹勾引儿子 淫荡的熟女处男无法拒绝', 'self-1811134'],['和单位女同事偷情自拍影片外流', 'self-1811135'],['国产真实老熟女丈母娘和女婿玩情趣虐待', 'self-1811136'],['高中女孩与爸爸做爱', 'self-1811137'],['國產母子乱伦！騷母親穿黑絲在门口迎接下班的儿子', 'self-1811138'],['大量潮吹雙穴噴出大量淫水', 'self-1811141'],['白虎姐妹穿鏤空性感網衣互慰互桶', 'self-1811142'],['在別人的墳墓前進行性交易，真的是太丧失了', 'self-1811143'],['老板包养野模白虎美鮑二奶', 'self-1811144'],['每個周末都會跟鄰居的媳婦約在樓梯間偷情', 'self-1811145'],['國產母子野外黑丝乱伦无套内射 对白淫荡', 'self-1811146'],['國產約炮約到內心狂野18歲少女去戶外野炮', 'self-1811147'],['國產亂倫在山上散步到涼亭直接亂倫幹起來', 'self-1811148'],['麻将高手三哥双飞经常搓牌少妇舒服了一笔勾销', 'self-1811149'],['91极品混血学妹魔鬼身材爆乳与富豪激情啪啪', 'self-1811151'],['國產直接在美景區搭訕單身小姐馬上給插', 'self-1811152'],['國產原創公園大膽偷情', 'self-1811153'],['搖一搖大膽美人妻要求一次插入雙穴', 'self-1811154'],['跟送貨的司機對眼直接搞上', 'self-1811155'],['與美鮑妻子在河堤野炮增加生活情趣', 'self-1811156'],['樓梯間攔截美人妻直接強行進入', 'self-1811157'],['熟女人妻取货时忘记穿内衣2 遭送貨員強迫揉乳', 'self-1811158'],['學生集體課堂上自慰', 'self-1811159'],['艺校美女身材诱人颜值好奶子嫩 可惜被非主流操了', 'self-1811161'],['和老婆闺蜜开房,这逼不省油，打炮前要调情', 'self-1811162'],['和邻居熟女张阿姨偷情', 'self-1811163'],['哥哥不要干 妈妈快回来了', 'self-1811164'],['家的96年小女儿！邊玩手機邊幫爸爸吃 爽歪歪呀', 'self-1811165'],['迷姦在拍攝威脅學妹不准傳出去', 'self-1811166'],['國產真實強姦親生女兒，爸：手拿走開', 'self-1811167'],['最喜歡讓穿著可愛睡衣的妹妹舒服', 'self-1811168'],['與超性感美顏巨乳一夜情', 'self-1811169'],['处女学妹变淫娃说不要操我,主人快停下全程国语', 'self-1811191'],['用肉棒狠狠抽插強姦同父異母的妹妹', 'self-1811192'],['刚开苞没多久的大一师妹来找我打炮 只好錄影紀念', 'self-1811193'],['国产母子操屄 剧情精彩对白淫荡', 'self-1811194'],['海某公司老闆跟已婚人妻下屬会计偷情-呻吟到-好滑呀', 'self-1811195'],['國產普通話调教刚收的大奶干女儿', 'self-1811196'],['晚自習闖入教職辦公室強姦女老師', 'self-1811197'],['搞完朋友的老婆再搞人家上大二的女儿', 'self-1811198'],['網紅主播露出難得一字美鮑自摸播出', 'self-1811199'],['一次迷姦妹妹帶回來的三個閨蜜姐妹們', 'self-1811211'],['老師利用職權性侵校內不良女學生', 'self-1811212'],['無時無刻都想在我體內的老公', 'self-1811213'],['猥瑣男撿屍高中生自拍影片扒光操爆', 'self-1811214'],['趁著老婆在煮菜 給女兒來個性教育', 'self-1811215'],['雙胞胎女兒的性教育要從小教起', 'self-1811216'],['獸父迷姦內射女兒同學 完事後還爽喝啤酒', 'self-1811217'],['獸父竟然對才讀國小的女兒下手', 'self-1811218'],['妈妈的圣水好喝吗国产母子乱伦', 'self-1811221'],['单位办公桌前自拍干少妇女同事', 'self-1811222'],['国内真实母子之间的秘密', 'self-1811223'],['某知名國中學生大膽在走廊上吃屌震驚校園', 'self-1811224'],['致命誘惑美顏巨乳無毛粉鮑白虎女神', 'self-1811225'],['真實姊弟亂倫1', 'self-1811226'],['迷姦過年拜訪的美乳小表妹倩倩', 'self-1811227'],['被強姦的大嫂忍不住羞辱大哭', 'self-1811228'],['搖一搖認識的白虎妹子答應讓我錄影留戀', 'self-1811229'],['手機自拍迷姦19歲少女', 'self-1811231'],['母子直播乱伦 证明母子关系还拿母亲的结婚照给大家看', 'self-1811232'],['同學會上衝動迷姦國中暗戀的同學', 'self-1811233'],['和老妈玩车震', 'self-1811234'],['国产熟女妈妈和儿子乱伦操屄 剧情精彩对白淫荡', 'self-1811235'],['妹妹熟睡時太可口忍不住插進去', 'self-1811236'],['真實姊弟亂倫2', 'self-1811237'],['被學長逼在外面脫衣服套頭性侵拍打霸凌', 'self-1811238'],['童雅妮飯店輪姦', 'self-1811239'],['本土白皙長腿高跟鞋辣妹露臉性愛自拍', 'self-1811261'],['國產刺激在熟睡的弟弟旁邊與女友打炮自拍影片', 'self-1811262'],['迷姦保險推銷員 大奶妹子還以為在做春夢', 'self-1811263'],['偷拍老師在辦公室用成績威脅學生做愛', 'self-1811264'],['國產真實禽獸執行規劃已久的迷姦計畫 ', 'self-1811265'],['童雅妮被幹的喊痛', 'self-1811266'],['逼國中學妹幫我吃屌 再將影片外流', 'self-1811267'],['與部門女同事每月一次的刺激三人行', 'self-1811268'],['ktv玩輸的脫一件 拖到後面直接幹起來', 'self-1811271'],['牛奶哥真实的朋友出国做生意和他老婆在家偷情', 'self-1811272'],['只要媽媽不再 爸爸就會叫我跟姐姐脫光陪看電視', 'self-1811273'],['被下迷藥的我奇癢難耐無法抗拒大肉棒', 'self-1811274'],['隔壁王太太還在哺乳時期找我偷情', 'self-1811275'],['辦公室飯後運動時間', 'self-1811276'],['(真實)酒店強奸女服務生', 'self-1811281'],['內射無毛粉穴小羅莉', 'self-1811282'],['好兄弟趁我不再與我媳婦偷情被我偷裝的攝影機錄下', 'self-1811283'],['身為攝影師最喜歡強姦剛出道的火辣嫩模', 'self-1811284'],['臭跩女同事被我迷姦後也只能乖乖被我操', 'self-1811285'],['高中發育青春期上完體育課後探索自己身體', 'self-1811286'],['暑假在家里约炮19岁的大一学妹露脸自拍', 'self-1811287'],['溪邊烤肉撞見情侶水中幹炮偷偷錄', 'self-1811288'],['4p东北绿帽奴的老婆浪叫滔天 ', 'self-1811291'],['去同學家補習大膽迷姦同學妹妹還自拍影片', 'self-1811292'],['好兄弟出去買東西把握機會迷姦他的白虎媳婦', 'self-1811293'],['自慰讓我三個乳房同時跳動', 'self-1811294'],['拜託把我所有的洞都塞得滿滿', 'self-1811295'],['迷姦獨自泡溫泉的年輕女性還偷拍影片 ', 'self-1811296'],['國產真實渣男下药在汽水中迷奸美女同事', 'self-1811297'],['深圳小姉喝多了被帶回酒店五花大綁錄影性侵', 'self-1811298'],['跟雙屌人做愛一次填滿兩個洞穴', 'self-1811299'],['台灣美女王思佳首映禮露點走光視頻', 'self-1811301'],['快用源源不絕的精液射滿我的嘴', 'self-1811302'],['快在我嘴中灌滿精液', 'self-1811303'],['兒子給老媽帶來了久違的快樂', 'self-1811304'],['國產網紅直播自慰粉美穴給哥哥們謀福利', 'self-1811305'],['部門兩個小妹為了升職而出賣肉體', 'self-1811306'],['閨蜜們之間的居家派對', 'self-1811307'],['夜晚寂寞叫個大奶妹子來陪陪老爺', 'self-1812031'],['青春期上網訂購自慰棒自拍影片傳到學校群組', 'self-1812032'],['剛那杯水喝了之後熱熱的好希望有人吸我雙乳插入我', 'self-1812033'],['國中情侶偷吃禁果還自拍錄影被外流', 'self-1812034'],['國產網紅主播一字馬自慰直播中', 'self-1812035'],['中國上海白嫩少婦女警制服誘惑老公 自拍性感幹炮', 'self-1812041'],['白絲女孩用假陽具自慰到白漿直流', 'self-1812042'],['网红云宝宝儿自拍黑丝吊带高跟雪白大波呻吟太销魂了', 'self-1812043'],['国产大奶萌妹子护士装小穴粉嫩-自慰求哥哥射她', 'self-1812044'],['偷拍母子做爱 ', 'self-1812045'],['本土OL白襯衫黑絲襪被撕破綑綁在椅子上性侵凌辱', 'self-1812046'],['被牛仔褲磨蹭到高潮', 'self-1812047'],['小伙啪啪老岳母', 'self-1812051'],['手指狂摳讓騷b潮吹放聲大叫求饒', 'self-1812052'],['打麻将输钱的老熟女用肉还债', 'self-1812053'],['多人衝擊我的所有敏感地帶高潮到抽蓄', 'self-1812054'],['妹妹就算是青春期也不能這樣誘惑哥哥亂倫', 'self-1812055'],['普通的雞巴滿足不了我的無毛粉穴', 'self-1812056'],['幹到小蘿莉向我的雞巴求饒', 'self-1812057'],['舔白虎粉鮑潮吹現喝淫水', 'self-1812058'],['少女玩弄自己无毛白嫩小穴制服網襪珍珠內', 'self-1812061'],['用三角錐自慰潮吹把地上都噴溼了', 'self-1812062'],['女神抹油自摸自慰替哥哥們謀福利', 'self-1812063'],['脹奶的粉色乳頭自慰奶水狂噴', 'self-1812064'],['國產網紅主播胸部彈出洋裝', 'self-1812065'],['最喜歡使用不同物品讓我高超噴水', 'self-1812066'],['幫無毛粉穴妹子拳交', 'self-1812067'],['癡女用仙人掌插入自慰', 'self-1812068'],['學長叫我發一段自慰視頻給他', 'self-1812071'],['表妹帶同學回家過夜，把她倆一塊迷魂了把她同學迷姦了 - 85VIDEOS', 'self-1812072'],['迷姦部門主管為平常被欺負報仇', 'self-1812073'],['網紅主播粉穴自慰淫語意淫各位哥哥們', 'self-1812074'],['攝影師把持不住直接插入模特', 'self-1812075'],['大叔只說了要給我糖果就叫我去他家', 'self-1812101'],['大冒險輸了懲罰在同學妹前幹炮內射', 'self-1812102'],['計畫已久在放學後迷姦學姊帶回家', 'self-1812103'],['爺爺不要啊!!!!!!!!!!!!!!1', 'self-1812104'],['禽獸 毛都還沒長期的少女也幹得下去', 'self-1812105'],['顏射在女兒同學的嬌嫩小臉上', 'self-1812106'],['借贷宝-刘慧', 'self-1812111'],['搖一搖竟然搖到女神即網紅', 'self-1812112'],['與我分手就惡意公開與女友的性愛影片', 'self-1812113'],['蜜月旅行在景點自拍唯美的性愛', 'self-1812114'],['變態癡漢尾隨放學落單女子迷姦', 'self-1812115'],['性愛成癮的黑絲大奶泡友', 'self-1812121'],['爸爸帶著朋友來我房間叫我忍一下就不痛了', 'self-1812122'],['國產从麻将馆出来以后发现一男人跟踪', 'self-1812123'],['國產讲淫语诱惑司机忍不住来操我', 'self-1812124'],['邊學開車邊操翻駕訓班教練', 'self-1812125'],['19岁上海南航空姐王琪，穿着空姐服就开肏', 'self-1812131'],['儿子的补课老师卫生间跪舔我的大鸡巴', 'self-1812132'],['大奶主播約經常刷跑車的粉絲到遊樂園戶外啪啪玩危險動作', 'self-1812133'],['初中表妹真温柔', 'self-1812134'],['國產白領誘惑王經理在廁所談公事 不管別人敲門', 'self-1812135'],['國產真實跟女邻居在公寓阳台疯狂偷情', 'self-1812136'],['國產網紅直播誘惑鮮肉網友幹自己', 'self-1812137'],['去同學家趁空檔大膽迷姦同學妹妹還自拍', 'self-1812141'],['玩遊戲的女友最後還是被我的大雞巴征服', 'self-1812142'],['某师范学院学妹与干爹第一次啪啪', 'self-1812143'],['國產真實勾引男友哥們上自己,', 'self-1812144'],['逼逼粉嫩多水的妹子被网友骗到出租屋不带套迷奸', 'self-1812145'],['東北女主播與老鐵粉絲在小樹林野戰', 'self-1812171'],['國產白领下夜班被色狼尾随拖到草丛强干对白精彩', 'self-1812172'],['國產網紅主播用鴨嘴在陰道中打顆雞蛋', 'self-1812173'],['國產騷貨網紅主播邀請羞澀網友到家直播誘惑', 'self-1812174'],['國產媽媽在野外穿黑絲用淫語誘惑親生兒子幹自己', 'self-1812175'],['網紅主播在ktv直播誘惑同型有人在包廂幹炮', 'self-1812176'],['大胆主播楠楠野外露出主动勾引摩的司机打野战', 'self-1812181'],['主播边插入小女友的肛门边让粉丝刷礼物', 'self-1812182'],['拳交女王各種讓自己爽翻天的方法', 'self-1812183'],['國產真實勾引男友哥們上自己,', 'self-1812184'],['溪邊玩水烤肉與隔壁哥哥對上眼直接幹起來', 'self-1812185'],['網紅主播跟網友約在野外直播幹炮', 'self-1812186'],['國產-用刷子跟拳頭幫我洗逼逼 白漿氾濫', 'self-1812191'],['國產-在浴室看到清潔刷忍不住塞入陰道內', 'self-1812192'],['國產性感女主播的小穴里什么都能塞', 'self-1812193'],['國產拳交女王周晓琳各种粗长玩具让她快乐呻吟', 'self-1812194'],['這根屌長到不可思議', 'self-1812195'],['國產性欲超强女主播 性感肚兜巨屌炮机玩弄骚逼', 'self-1812201'],['國產女主播直播泉交給各位哥哥們欣賞', 'self-1812202'],['國產各种器具轮流放屄里自慰到高潮', 'self-1812203'],['國產学生装肉丝拿超大假屌爆插小穴', 'self-1812204'],['國產粉穴網紅主播將橘子塞入肛門止癢', 'self-1812205'],['18岁性感学妹在豪宅享受干爹的大鸡吧', 'self-1812211'],['叶美上演夫妻激情大秀，多姿势激情爆草口活淫语太刺激了', 'self-1812212'],['假雞巴也能讓我的小穴狂噴淫水', 'self-1812213'],['國產女神主播搔首弄姿自慰白漿狂噴', 'self-1812214'],['國產黑絲女神直播表演甚麼叫白漿噴泉', 'self-1812215'],['國產性感网红穿着红肚兜勾引冰箱售后维修員', 'self-1812221'],['喜歡粗棒虐待我的粉美穴', 'self-1812222'],['国产超诱惑熟女直播多花样伺候小鲜肉 超刺激 2', 'self-1812223'],['国产超诱惑熟女直播多花样伺候小鲜肉 超刺激 3', 'self-1812224'],['国产超诱惑熟女直播多花样伺候小鲜肉 4', 'self-1812225'],['舞蹈老师淫乱视频流出国语对白', 'self-1812226'],['上海车模李雅穿豹紋連身內衣跟我幹炮留念', 'self-1812251'],['下班與閨蜜小酌後勾起性慾互磨豆腐', 'self-1812252'],['國產大奶鄰居半夜穿透視內衣按電鈴誘惑我', 'self-1812253'],['性欲來襲幻想被大雞巴填滿一邊自慰噴水', 'self-1812254'],['在健身房迷姦白虎少女盡情抽插虎穴', 'self-1812255'],['成都老熟女 和儿子性交', 'self-1812261'],['老公出差不回來 只好買個玩具填滿洞穴', 'self-1812262'],['希望哥哥們能看著我的粉嫩穴打飛機', 'self-1812263'],['姊姊說我成年了要教我性愛的技巧', 'self-1812264'],['放學後淫想著學長一邊錄影自慰摳弄粉穴', 'self-1812265'],['國產女神自慰喷水大秀', 'self-1812266'],['國產網紅主播勾搭个工厂打工仔到野外高压电架下野战', 'self-1812267'],['淘宝小野模大长腿丝袜诱惑', 'self-1812268'],['視訊網友性感小野貓自慰給我互相電愛', 'self-1812269'],['綑綁著被性虐待卻得到一絲高潮', 'self-18122610'],['閨蜜說想讓我體會甚麼叫高潮', 'self-18122611'],['好想立刻插入這性感騷貨主播', 'self-1812281'],['網紅主播大半夜的求哥哥們抽插他', 'self-1812282'],['學姊半夜傳這影片給我說他家沒大人', 'self-1812283'],['我的粉騷穴好癢阿', 'self-1901021'],['真希望我的粉嫩酥胸被大力揉捏', 'self-1901022'],['國產性感小模不停色誘攝影師哥哥', 'self-1901023'],['騷女僕等待大雞巴的哥哥抽插', 'self-1901024'],['OL女神第一次完美3p内心渴望被双插', 'self-1901031'],['國產20岁嫩老婆首次3P前后洞被干潮吹', 'self-1901032'],['國產真實條叫媳婦與兄弟矇眼3P', 'self-1901033'],['國產騷主播賣力抽插自己粉穴', 'self-1901034'],['終於幹到企劃部門小倩', 'self-1901035'],['替我家小護士打一針', 'self-1901036'],['調教高中女學生', 'self-1901037'],['一个淫妻的老公最爱看老婆被单男猛操 還兼攝影', 'self-1901041'],['不小心在沙發上睡著被父親朋友硬上', 'self-1901042'],['青春期沒長毛的好姊妹一起探索身體', 'self-1901043'],['偷情鄰居才發現賺到一個無毛粉鮑', 'self-1901044'],['國產思妍小仙女之性感长腿女神被猛幹', 'self-1901045'],['國產特别企划找真实空姐', 'self-1901071'],['國產跟老婆承認性癖好後帶老婆嘗試5P', 'self-1901072'],['國產調教黑絲美乳騷主播', 'self-1901073'],['無毛粉鮑吃起來有種甜甜的味道', 'self-1901074'],['幹朋友妹妹18歲毛沒長齊的少女', 'self-1901075'],['感覺性經驗豐富的18歲高中女孩', 'self-1901076'],['操爆Cosplay騷網友', 'self-1901077'],['为了我的蜜穴你愿意花多少钱', 'self-1901081'],['台灣 2017世大運 選手被台妹約砲 長髮女享受洋腸', 'self-1901082'],['台灣本土無碼 高雄巨乳超粉嫩妹 罕見一線鮑魚', 'self-1901083'],['台灣某服飾店老闆娘醉後淫蕩到不行', 'self-1901084'],['台灣無碼 剛滿18第一次接客被我碰到', 'self-1901085'],['我的穴穴把哥哥的車子用溼了', 'self-1901086'],['無毛粉穴裡無止盡噴射的淫水', 'self-1901087'],['網紅 萌白醬 玻璃棒插入粉嫩蜜穴~淫水直流~浪叫不斷', 'self-1901088']];
		

		$countTitles = 0;
		foreach($datas as $key => $data)  
		{
			foreach($avkeyTitles as $avkeyTitle)  
			{
				if( $data['avkey'] == $avkeyTitle[1] )
				{
					$datas[$key]['title'] = $avkeyTitle[0];
					$datas[$key]['content'] = $avkeyTitle[0];
					$countTitles++;
				}
			}
		}

		//As = [ ['avkey', duration, title, content, views, video_source, video_type, cover_index, tags] ]
		$tempStr1 = "[['";
		//$tempStr1 = "['";   //只撈avkey

		foreach($datas as $data)  
		{
			$str_time = $data['duration'];
			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
			//$tempStr1 = $tempStr1 . $data['avkey'] . "', " . $time_seconds . "], ['";
			//$tempStr1 = $tempStr1 . $data['avkey'] . ", ";   //只撈avkey
			/*
			//As = [ ['avkey', duration, title, content, views, video_source, video_type, cover_index, tags] ]
			$tempStr1 = $tempStr1 . $data['avkey'] . "', " 
						. $time_seconds . ", '" 
						. $data['title'] . "', '" 
						. $data['content'] . "', "
						. $data['views'] .", " 
						. $data['video_source'] .", " 
						. $data['video_type'] .", " 
						. $data['cover_index'] .", '" 
						. $data['tags'] 
						. "'], ['";
			*/
			//As = [ ['avkey', duration, title, content, video_source, video_type] ]
			$tempStr1 = $tempStr1 . $data['avkey'] . "', " 
						. $time_seconds . ", '" 
						. $data['title'] . "', '" 
						. $data['content'] . "', "
						. $data['video_source'] .", " 
						. $data['video_type']
						. "], ['";


		}

		dd($tempStr1);	
	
	}


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
	 //最新影片區
    public function index()
    {
		//$avkeys = \App\AvVideo::where('enable','on')->orderBy('release_date','desc')->pluck('avkey','id')->take(8)->toArray();
		$avkeys = \App\AvVideo::where('enable','on')->where('video_source',3)->orderBy('updated_at','desc')->pluck('avkey','id')->take(6)->toArray();
		//$links = $this->paginate($avkeys, $this->pageNumber)->links();
		$NewVideos = array();
		foreach ($avkeys as $avkey){
			$NewVideos[] = $this->getVideo($avkey);
		}

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',1)->orderBy('updated_at','desc')->pluck('avkey','id')->take(6)->toArray();
		//$links = $this->paginate($avkeys, $this->pageNumber)->links();
		$JapanVideos = array();
		foreach ($avkeys as $avkey){
			$JapanVideos[] = $this->getVideo($avkey);
		}

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',0)->where('video_type',0)->orderBy('updated_at','desc')->pluck('avkey','id')->take(8)->toArray();
		// $links = $this->paginate($avkeys, $this->pageNumber)->links();
		$HDJapanVideos = array();
		foreach ($avkeys as $avkey){
			$HDJapanVideos[] = $this->getVideo($avkey);
		}

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',1)->orderBy('updated_at','desc')->pluck('avkey','id')->take(8)->toArray();
		//$links = $this->paginate($avkeys, $this->pageNumber)->links();
		$HDUnitedVideos = array();
		foreach ($avkeys as $avkey){
			$HDUnitedVideos[] = $this->getVideo($avkey);
		}

		// 卡通
		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',2)->orderBy('updated_at','desc')->pluck('avkey','id')->take(8)->toArray();
		//$links = $this->paginate($avkeys, $this->pageNumber)->links();
		$HDCartoonVideos = array();
		foreach ($avkeys as $avkey){
			$HDCartoonVideos[] = $this->getVideo($avkey);
		}

		// 自拍
		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',3)->orderBy('updated_at','desc')->pluck('avkey','id')->take(8)->toArray();
		//$links = $this->paginate($avkeys, $this->pageNumber)->links();
		$HDSelfVideos = array();
		foreach ($avkeys as $avkey){
			$HDSelfVideos[] = $this->getVideo($avkey);
		}
	
		// IndexVideoArray is used for new hot 4-type av in home page
		$key = 'IndexVideosJson'; //首頁五宮格
		$IndexVideosJson = Redis::get($key);
		if (is_null($IndexVideosJson)) {

			$IndexVideosJson = json_decode(config('IndexVideosJson'), true);
			$IndexVideoArray = [];
			if($IndexVideosJson){
				foreach ($IndexVideosJson as $index => $avkeys){
					foreach ($avkeys as $avkey){
						$IndexVideoArray[$index][] = $this->getVideo($avkey);
					}
				}
			}

			$IndexVideoArray = json_encode($IndexVideoArray);
			Redis::setex($key, 86400, $IndexVideoArray);
			$IndexVideosJson = $IndexVideoArray;
		}
		$IndexVideoArray = json_decode($IndexVideosJson, true);



		$key = 'IndexBannerJson';
		$IndexBannerJson = Redis::get($key);

		if (is_null($IndexBannerJson)) {
			$current = date('Y-m-d H:i:s', time());
			$IndexBannerArray = \App\Banner::where('enable', '1')
				->where('start_time', '<', $current)
				->where('end_time', '>', $current)
				->orderBy('id', 'desc')
				//->orderBy('id', 'dec')
				->get()
				->toArray();
				
			$IndexBannerArray = json_encode($IndexBannerArray);
			Redis::setex($key, 300, $IndexBannerArray);
			$IndexBannerJson = $IndexBannerArray;
		}
		$IndexBannerArray = json_decode($IndexBannerJson, true);

		// 取得目前啟動中的Banners
        // $current = date('Y-m-d H:i:s', time());
        // $IndexBannerArray = \App\Banner::where('enable', '1')
            // ->where('start_time', '<', $current)
            // ->where('end_time', '>', $current)
            // ->get()
            // ->toArray();


		return view('index',[
		    'NewVideos' => $NewVideos,
            'JapanVideos' => $JapanVideos,
            'HDJapanVideos' => $HDJapanVideos,
            'HDUnitedVideos' => $HDUnitedVideos,
			'HDCartoonVideos' => $HDCartoonVideos,
			'HDSelfVideos' => $HDSelfVideos,
            'IndexVideoArray' => $IndexVideoArray,
            'IndexBannerArray' => $IndexBannerArray
        ]);
    }
	//最新影片
	/*
	public function latest(){


		//$avkeys = \App\AvVideo::where('enable','on')->where('is_free',1)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$avkeys = \App\AvVideo::where('enable','on')->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('latest',['AvVideos' => $AvVideos, 'links' => $links]);
	}*/

	//最新影片
	public function hdLatest(){

	    //取得標籤順序
        //$tagArray = $this->getTags();


		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}


		return view('hd-latest',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            //'tagArray' => $tagArray
        ]);
	}
	//高清有码
	public function hdCensored(){

        //取得標籤順序
        //$tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',0)->where('video_type',1)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '日韩有码',
            //'tagArray' => $tagArray
            ]);
	}
	//高清无码
	public function hdUncensored(){

        //取得標籤順序
        //$tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',0)->where('video_type',0)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '日韩无码',
            //'tagArray' => $tagArray
        ]);
	}
	//高清欧美
	public function hdUnited(){

        //取得標籤順序
        //$tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',1)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '欧美影片',
           // 'tagArray' => $tagArray
        ]);
	}
	//高清卡通
	public function hdCartoon(){

        //取得標籤順序
        //$tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',2)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '成人动画',
            //'tagArray' => $tagArray
        ]);
	}
	//高清自拍
	public function hdSelf(){

        //取得標籤順序
        //$tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',0)->where('video_source',3)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '成人短片',
           // 'tagArray' => $tagArray
        ]);
	}
	//免費影片
	public function hdFree(){

        //取得標籤順序
       // $tagArray = $this->getTags();

		$avkeys = \App\AvVideo::where('enable','on')->where('is_free',1)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('hd-list',[
		    'AvVideos' => $AvVideos,
            'links' => $links,
            'tag' => '免費影片',
           // 'tagArray' => $tagArray
        ]);
	}


	//類別找片
	public function category(){

		return view('category',[]);
	}

	 
	 //自拍影片
    public function self()
    {
		$avkeys = \App\AvVideo::where('enable','on')->where('video_source',3)->orderBy('updated_at','desc')->pluck('avkey','id')->toArray();
		$links = $this->paginate($avkeys, $this->pageNumber)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}

		return view('self',['AvVideos' => $AvVideos, 'links' => $links]);
    }

	

	//手動分頁
	private function paginate(&$items,$perPage) {
		return \App\Classes\Common::paginate($items,$perPage);
	}

	private function getVideo($avkey) {
		return \App\Classes\Common::getVideo($avkey);
	}
	private function getActor($id) {
		return \App\Classes\Common::getActor($id);
	}

    //取得標籤順序
    private function getTags(){

		$key = 'Top6TagsJson';
		$Top6TagsJson = Redis::get($key);

		if (is_null($Top6TagsJson)) {
			$tags = config('tags_order');
			redis::setex('Top6TagsJson',86400, $tags);
		}
		else {
			$tags = $Top6TagsJson;
		}

		// $tags = $Top6TagsJson;

        $tags = json_decode($tags, true);

        $tmp = [];
        foreach($tags as $key => $item){
            $tmp[] = explode('|', $item);
        }
        return $tmp;
    }
}
