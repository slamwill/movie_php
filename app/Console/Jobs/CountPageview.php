<?php
/**
 * Created by PhpStorm.
 * User: yuan
 * Date: 2018/7/9
 * Time: 上午10:14
 */

namespace App\Console\Jobs;

use App\Pageview;
use App\ReportCategoryMonth;
use App\ReportMonth;
use App\ReportTagMonth;
use App\ReportWatchMonth;
use App\User;
use App\VwPageviewCategory;
use App\VwPageviewTag;
use App\VwPageviewWatch;
use Illuminate\Support\Facades\Redis;

class CountPageview
{
    public $ymd;

    public $seq;

    public function __construct($date = null)
    {
        if(is_null($date)) {
            $this->ymd = date('Ymd', strtotime('yesterday'));
        }else{
            $this->ymd = date('Ymd', strtotime($date));
        }
        $this->seq = 1;
    }

    public function setYmd($date = null)
    {
        if(is_null($date)) return;
        $this->ymd = date('Ymd', strtotime($date));
    }

    public function recountPageViewCategory()
    {
        // 移除舊的紀錄
        ReportCategoryMonth::where('date', $this->ymd)->delete();
        $this->countPageviewCategory();
    }

    public function recountPageviewWatch()
    {
        // 移除舊的紀錄
        ReportWatchMonth::where('date', $this->ymd)->delete();
        $this->countPageviewWatch();
    }

    public function recountPageviewTag()
    {
        // 移除舊的紀錄
        ReportTagMonth::where('date', $this->ymd)->delete();
        $this->countPageviewTag();
    }

    public function recountPageviewDaily()
    {
        // 移除舊的紀錄
        ReportMonth::where('date', $this->ymd)->delete();
        $this->countPageviewDaily();
    }

    public function countPageviewCategory()
    {
        $this->countCategory(0);
        $this->countCategory(1);
    }

    public function countPageviewTag()
    {
        $this->countTag(0);
        $this->countTag(1);
    }

    public function countPageviewWatch()
    {
        $this->countWatch(0);
        $this->countWatch(1);
    }

    public function countPageviewDaily()
    {
        $this->seq = 1;

        // 統計導入數
        $this->countReferrerLike('www.1778mao.com');

        // 統計點擊註冊數
        $this->countRegisterClick();

        // 統計註冊成功數
        $this->countRegistered();

        // 統計網站單日瀏覽數
        $this->countSitePageview();

        // 統計網站單日瀏覽數：到訪網站的不重複IP數
        $this->countSitePageviewByIP();

        // 統計點擊Banner數
        $this->countBanner();

        /*
         * 依照分類區分當日總瀏覽數
         *   熟女人妻當日總瀏覽數、絲襪制服當日總瀏覽數、美顏巨乳當日總瀏覽數、S級女優當日總瀏覽數、中文無碼當日總瀏覽數
         *   中文有碼當日總瀏覽數、網紅主播當日總瀏覽數、國產換妻當日總瀏覽數、歐美電影當日總瀏覽數、成人動漫當日總瀏覽數
         *   經典三級當日總瀏覽數、開放90後當日總瀏覽數、手機小視頻當日總瀏覽數、SM系列當日總瀏覽數、高清日韓有碼當日總瀏覽數
         *   高清日韓無碼當日總瀏覽數、高清歐美影片當日總瀏覽數 ... etc
         */
        $this->countCategoryFromReport();

        /*
         * 依照標籤區分當日總瀏覽數
         *   TAG1當日總瀏覽數、TAG2當日總瀏覽數 ... etc
         */
        $this->countTagFromReport();

    }

    private function countCategory($login = 0)
    {
        // 先檢是否已有記錄
        if(ReportCategoryMonth::where('date', $this->ymd)
                                ->where('login', $login)
                                ->get()->count())
            return;

        // 取得最高的10筆
        $vw_data = VwPageviewCategory::where('date', $this->ymd)
            ->where('login', $login)
            ->orderBy('views', 'desc')
            ->select(['date', 'record', 'login', 'views'])
            ->take(10)
            ->get()
            ->toArray();
        // 解析record & 寫入報表
        foreach($vw_data as $key => $value){
            $value['title'] = preg_replace(
                ['#[a-zA-Z]{0,6}\/(.+)#im', '#(.+)\/.*#im'],
                ['$1', '$1'],
                $value['record']
            );
            $value['sequence'] = $key + 1;
            ReportCategoryMonth::insert($value);
        }
    }

    private function countTag($login = 0)
    {
        // 先檢是否已有記錄
        if(ReportTagMonth::where('date', $this->ymd)
            ->where('login', $login)
            ->get()->count())
            return;

        // 取得最高的10筆
        $vw_data = VwPageviewTag::where('date', $this->ymd)
            ->where('login', $login)
            ->orderBy('views', 'desc')
            ->select(['date', 'record', 'login', 'views'])
            ->take(10)
            ->get()
            ->toArray();
        // 解析record & 寫入報表
        foreach($vw_data as $key => $value){
            $value['title'] = preg_replace(
                ['#[a-zA-Z\/]{0,6}\/(.+)#im'],
                ['$1'],
                $value['record']
            );
            $value['sequence'] = $key + 1;
            ReportTagMonth::insert($value);
        }
    }

    private function countWatch($login = 0)
    {
        // 先檢是否已有記錄
        if(ReportWatchMonth::where('date', $this->ymd)
            ->where('login', $login)
            ->get()->count())
            return;

        // 取得最高的10筆
        $vw_data = VwPageviewWatch::where('date', $this->ymd)
            ->where('login', $login)
            ->orderBy('views', 'desc')
            ->select(['date', 'record', 'login', 'views'])
            ->take(10)
            ->get()
            ->toArray();
        // 解析record & 寫入報表
        foreach($vw_data as $key => $value){
            $value['title'] = preg_replace(
                ['#[a-zA-Z\/]{0,9}\/(.+)#im', '#(.+)\/.*#im'],
                ['$1', '$1'],
                $value['record']
            );
            $value['sequence'] = $key + 1;
            ReportWatchMonth::insert($value);
        }
    }

    // 統計導入數
    private function countReferrerLike($referrer = null)
    {
        if(empty($referrer)) return;

        // 是否已有記錄
        if(ReportMonth::where('date', $this->ymd)
            ->where('title', $referrer . '導入數')
            ->get()->count())
            return;

        $views = Pageview::where('date', $this->ymd)
            ->where('type', 'ref')
            ->where('record', 'like', sprintf('%%%s%%', $referrer))
            ->sum('views');
        // login = 3, 因為非未登入也非已登入
        ReportMonth::insert(['date' => $this->ymd, 'login' => 3, 'title' => $referrer . '導入數', 'views' => $views, 'sequence' => $this->seq]);

        $this->incrSeq(1);

        return $views;
    }

    // 統計點擊註冊數
    private function countRegisterClick()
    {
        // 是否已有記錄
        if(ReportMonth::where('date', $this->ymd)
            ->where('title', '點擊註冊數')
            ->get()->count())
            return;

        $views = Pageview::where('date', $this->ymd)
            ->where('type', 'uri')
            ->where('record', 'like', 'register%')
            ->where('login', 0)
            ->sum('views');
        ReportMonth::insert(['date' => $this->ymd, 'login' => 0, 'title' => '點擊註冊數', 'views' => $views, 'sequence' => $this->seq]);

        $this->incrSeq(1);

        return $views;
    }

    // 統計註冊成功數
    private function countRegistered()
    {
        // 是否已有記錄
        if(ReportMonth::where('date', $this->ymd)
            ->where('title', '註冊成功數')
            ->get()->count())
            return;

        $start = date('Y-m-d H:i:s', strtotime($this->ymd .'000000'));
        $end = date('Y-m-d H:i:s', strtotime($this->ymd .'235959'));
        $views = User::whereBetween('created_at', [$start, $end])
            ->get()
            ->count();
        ReportMonth::insert(['date' => $this->ymd, 'login' => 3, 'title' => '註冊成功數', 'views' => $views, 'sequence' => $this->seq]);

        $this->incrSeq(1);

        return $views;
    }

    // 統計網站單日瀏覽數
    private function countSitePageview()
    {
        // 是否已有記錄
        if(ReportMonth::where('date', $this->ymd)
            ->where('title', '網站單日瀏覽數')
            ->get()->count())
            return;

        $views = Pageview::where('date', $this->ymd)
            ->where('type', 'uri')
            ->get()
            ->sum('views');
        ReportMonth::insert(['date' => $this->ymd, 'login' => 3, 'title' => '網站單日瀏覽數', 'views' => $views, 'sequence' => $this->seq]);

        $this->incrSeq(1);

        return $views;
    }

    // 統計網站單日瀏覽數：到訪網站的不重複IP數
    private function countSitePageviewByIP()
    {
        // 是否已有記錄
        if(ReportMonth::where('date', $this->ymd)
            ->where('title', '到訪網站的不重複IP數')
            ->get()->count())
            return;

        $views = Pageview::where('date', $this->ymd)
            ->where('type', 'ip')
            ->get()
            ->count();
        ReportMonth::insert(['date' => $this->ymd, 'login' => 3, 'title' => '到訪網站的不重複IP數', 'views' => $views, 'sequence' => $this->seq]);

        $this->incrSeq(1);

        return $views;
    }

    // 統計點擊Banner數
    private function countBanner()
    {
        // 取得banner相關的紀錄
        $raw = Pageview::where('date', $this->ymd)
            ->where('type', 'uri')
            ->where('record', 'like', 'banner%')
            ->get()
            ->toArray();
        foreach($raw as $key => $row){
            // 分析 title
            $row['title'] = 'Banner編號[%d]點擊數';
            $row['title'] = sprintf($row['title'], preg_replace(
                ['#[a-zA-Z\/]{0,9}\/(.+)#im', '#(.+)\/.*#im'],
                ['$1', '$1'],
                $row['record']
            ));
            $row['sequence'] = $this->seq;
            $row['login'] = 3;
            unset($row['id']);
            unset($row['type']);
            unset($row['record']);

            // 檢查是否已有記錄
            if(ReportMonth::where('date', $this->ymd)->where('title', $row['title'])->get()->count())
                continue;

            // 寫入記錄
            ReportMonth::insert($row);
        }

        $this->incrSeq(1);

        return $raw;
    }

    // 依照分類區分 統計當日總瀏覽數
    private function countCategoryFromReport()
    {
        // 取得相關的紀錄
        $raw = ReportCategoryMonth::where('date', $this->ymd)
            ->select(['date', 'title', 'views'])
            ->get()
            ->toArray();

        // 先進行一次統計
        $rows = [];
        foreach($raw as $key => $row){
            if(!isset($rows[$row['title']])) {
                $rows[$row['title']] = $row;
                $rows[$row['title']]['sequence'] = $this->seq;
                $rows[$row['title']]['login'] = 3;
            }else{
                $rows[$row['title']]['views'] += $row['views'];
            }
        }

        // 寫入記錄
        foreach($rows as $key => $row){
            $row['title'] = sprintf('[%s]當日總瀏覽數', $row['title']);

            // 檢查是否已有記錄
            if(ReportMonth::where('date', $this->ymd)->where('title', $row['title'])->get()->count())
                continue;

            // 寫入記錄
            ReportMonth::insert($row);
        }

        $this->incrSeq(1);

        return $rows;
    }

    // 依照標籤區分 統計當日總瀏覽數
    private function countTagFromReport()
    {
        // 取得相關的紀錄
        $raw = ReportTagMonth::where('date', $this->ymd)
            ->select(['date', 'title', 'views'])
            ->get()
            ->toArray();

        // 先進行一次統計
        $rows = [];
        foreach($raw as $key => $row){
            if(!isset($rows[$row['title']])) {
                $rows[$row['title']] = $row;
                $rows[$row['title']]['sequence'] = $this->seq;
                $rows[$row['title']]['login'] = 3;
            }else{
                $rows[$row['title']]['views'] += $row['views'];
            }
        }

        // 寫入記錄
        foreach($rows as $key => $row){
            $row['title'] = sprintf('[%s]當日總瀏覽數', $row['title']);

            // 檢查是否已有記錄
            if(ReportMonth::where('date', $this->ymd)->where('title', $row['title'])->get()->count())
                continue;

            // 寫入記錄
            ReportMonth::insert($row);
        }

        $this->incrSeq(1);

        return $rows;
    }

    private function incrSeq($int = 0)
    {
        if(empty($int)) return;
        $this->seq += $int;
    }

    public function countPageviewFiveMinutes()
    {
        Redis::select(1);
        $redis_key = 'VideoViews:';

        foreach(Redis::keys($redis_key . '*') as $key){
			list($prefix, $VideoID) = explode(':', $key);
			$VideoViews = \App\AvVideo::find($VideoID);
			if ($VideoViews) {
				$VideoViews->timestamps = false;
				$VideoViews->increment('views' , Redis::get($key));
			}
			Redis::del($key);
        }
        Redis::select(0);
    }
}