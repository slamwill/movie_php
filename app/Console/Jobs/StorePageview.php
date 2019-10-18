<?php
/**
 * Created by PhpStorm.
 * User: yuan
 * Date: 2018/7/9
 * Time: 上午10:14
 */

namespace App\Console\Jobs;

use App\Pageview;
use Illuminate\Support\Facades\Redis;

class StorePageview
{
    private $prefix = 'avcms';
    public $ymd;

    public function __construct()
    {
        $this->ymd = (time() - strtotime('today midnight') > 600)
            ? date('Ymd')
            : date('Ymd', strtotime('today midnight -1 sec'));
    }

    // type = uri | ip | ref
    public function exec($type = 'uri')
    {
        $redis_key = sprintf('%s:%s:%s*', $this->prefix, $type, $this->ymd);

        switch($type){
            case 'ip':
                $this->recordIP($redis_key);
                break;

            case 'ref':
                $this->recordREF($redis_key);
                break;

            case 'uri':
            default:
                $this->recordURI($redis_key);
                break;
        }
    }

    private function recordURI($redis_key = null)
    {
        if(is_null($redis_key)) return;

        Redis::select(1);
        foreach(Redis::keys($redis_key . '*') as $key){
            list($prefix, $type, $date, $login, $record) = explode(':', $key);
            $uri = urldecode($record);
            $views = Redis::get($key);

            $pageview = new Pageview();
            $row = $pageview->where('type', $type)
                ->where('date', $date)
                ->where('login', $login)
                ->where('record', $uri)
                ->take(1)->get()->toArray();
            if(empty($row)){
                $pageview->type = $type;
                $pageview->date = $date;
                $pageview->record = $uri;
                $pageview->login = $login;
                $pageview->views = $views;
                $pageview->save();
            }else{
                Pageview::where('id', $row[0]['id'])->update(['views' => $row[0]['views'] + $views]);
            }
            Redis::del($key);
        }
    }

    private function recordREF($redis_key = null)
    {
        if(is_null($redis_key)) return;

        Redis::select(1);
        foreach(Redis::keys($redis_key . '*') as $key){
            list($prefix, $type, $date, $login, $record) = explode(':', $key);
            $referrer = urldecode(base64_decode($record));
            $views = Redis::get($key);

            $pageview = new Pageview();
            $row = $pageview->where('type', $type)
                ->where('date', $date)
                ->where('login', $login)
                ->where('record', $referrer)
                ->take(1)->get()->toArray();
            if(empty($row)){
                $pageview->type = $type;
                $pageview->date = $date;
                $pageview->record = $referrer;
                $pageview->login = $login;
                $pageview->views = $views;
                $pageview->save();
            }else{
                Pageview::where('id', $row[0]['id'])->update(['views' => $row[0]['views'] + $views]);
            }
            Redis::del($key);
        }
    }

    private function recordIP($redis_key = null)
    {
        if(is_null($redis_key)) return;

        Redis::select(1);
        foreach(Redis::keys($redis_key . '*') as $key){
            list($prefix, $type, $date, $login, $record) = explode(':', $key);
            $ip = $record;
            $views = Redis::get($key);

            $pageview = new Pageview();
            $row = $pageview->where('type', $type)
                ->where('date', $date)
                ->where('login', $login)
                ->where('record', $ip)
                ->take(1)->get()->toArray();
            if(empty($row)){
                $pageview->type = $type;
                $pageview->date = $date;
                $pageview->record = $ip;
                $pageview->login = $login;
                $pageview->views = $views;
                $pageview->save();
            }else{
                Pageview::where('id', $row[0]['id'])->update(['views' => $row[0]['views'] + $views]);
            }
            Redis::del($key);
        }
    }
}