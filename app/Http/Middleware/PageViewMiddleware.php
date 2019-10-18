<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PageViewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $prefix = "avcms";
        /*
         * before middleware
         * 透過 schedule 定期將 Redis 寫入 avcms.pageview
         */
        $date = date('Ymd');
        $login = Auth::check() ? 1 : 0;

        // 用 <Prefix>:<類型>:<日期>:<登入狀態>:<URI> 當作key
        $uri = $this->parserURI($request->path());;
        $key = sprintf('%s:%s:%s:%d:%s', $prefix, 'uri', $date, $login, $uri);
        Redis::select(1);
        Redis::incr($key);
        Redis::expire($key, 86400 * 2);

        // 用 <Prefix>:<類型>:<日期>:<登入狀態>:<IP> 當作key
        $clientIp = $request->ip();
        $key = sprintf('%s:%s:%s:%d:%s', $prefix, 'ip', $date, $login, $clientIp);
        Redis::select(1);
        Redis::incr($key);
        Redis::expire($key, 86400 * 2);

        // 用 <Prefix>:<類型>:<日期>:<登入狀態>:<referrer> 當作key
        $referrer = $request->server('HTTP_REFERER');
        $host = $request->server('HTTP_HOST');
        if(!strpos($referrer, $host)){
            $key = sprintf('%s:%s:%s:%d:%s', $prefix, 'ref', $date, $login, base64_encode($referrer));
            Redis::select(1);
            Redis::incr($key);
            Redis::expire($key, 86400 * 2);
        }

        Redis::select(0); // 復歸, 避免影響原本的功能

        $response = $next($request);

        /*
         * after middleware
         */

        return $response;
    }

    /**
     * @param $path
     * @return mixed
     * 針對特定的uri做解析
     */
    private function parserURI($path){
        return preg_replace_callback('#(banner\/)(.*)#s', function($matches){
            $string = $matches[2];
            $string = \Crypt::decrypt($string);
            if (!$string) return;

            list($id, $url) = explode('|', $string);

            return $matches[1] . $id;
        }, $path);
    }
}
