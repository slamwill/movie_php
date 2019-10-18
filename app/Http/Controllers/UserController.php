<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Hash;
//use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\User;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
	}



	//修改email
	public function emailUpdate(Request $request){

		$validator = Validator::make(
			$request->all(),
			[
				'email' => 'required|email|unique:users'
			],
			[
				'email.required' => '邮箱不能空白',
				'email.email' => '请输入邮箱格式',
				'email.unique' => '该邮箱已被注册',
			]
		);
		if ($validator->fails())
		{
			return response()->json(['status' => 0, 'message' => $validator->messages()->first()]);
			
		}

		$user = \App\User::find(Auth::id());
		$user->email = $request->email;
		$user->save();

		return response()->json(['status' => 1, 'message' => '保存完成']);

	}

	//修改密码
	public function passwordUpdate(Request $request){


		$this->validate($request, [
			'curPassword' => 'required|min:6',
			'newPassword' => 'required|min:6|confirmed',
        ],[
			'curPassword.required' => '当前密码不能空白',	
			'curPassword.min' => '当前密码最少 :min 个字元',	
			'newPassword.required' => '新密码不能空白',	
			'newPassword.min' => '新密码最少 :min 个字元',	
			'newPassword.confirmed' => '新密码与新密码确认不相同',
		]);

		if (Hash::check($request->curPassword, Auth::User()->password) == false){
			return redirect()->back()->withInput()->withErrors(['message' => '当前密码错误']);		
		}


		$user = \App\User::find(Auth::id());
		$user->password = Hash::make($request->newPassword);
		$user->save();

		return redirect()->back()->with(['message' => '密码修改完成']);


	}
	//交易VIP
	public function transferVIP(Request $request){

		$validator = Validator::make(
			$request->all(),
			[
				'id' => 'required|integer'
			],
			[
			]
		);
		if ($validator->fails())
		{
			return response()->json(['status' => 0, 'message' => $validator->messages()->first()]);
		}
		$ServiceConfig = \App\ServiceConfig::where('id',$request->id)->where('enable','on')->first();
		if (!$ServiceConfig) {
			return response()->json(['status' => 0, 'message' => '方案异常']);
		}

		if(Auth::user()->coins - $ServiceConfig->coins < 0) {
			return response()->json(['status' => 2, 'message' => '您的点数不足，是否前往充值?']);
		}


		$user = Auth::user();
		$oldCoins = $user->coins;
		$user->decrement('coins', $ServiceConfig->coins);

		$old_expired = $user->expired;
		if (strtotime($old_expired) < time()) $old_expired = date('Y-m-d H:i:s', time());
		$new_expired = date('Y-m-d H:i:s', strtotime($old_expired) + 60 * $ServiceConfig->times);
		$user->expired = $new_expired;
		$user->save();
		$json = [
			'coins' => ['from' => $oldCoins, 'to' => $user->coins],
			'expired' => ['old_expired' => $old_expired, 'new_expired' => $new_expired],
			'service' => $ServiceConfig->toArray(),
		];
		$TransferLog = new \App\TransferLog;
		$TransferLog->order_no = uniqid(date('ymdhis'));
		$TransferLog->user_id = $user->id;
		$TransferLog->coins = -$ServiceConfig->coins;
		$TransferLog->user_coins = $user->coins;
		$TransferLog->type = 2;//config('av.translog')
		$TransferLog->parent_id = $ServiceConfig->id;
		$TransferLog->json = $json;
		$TransferLog->memo = $ServiceConfig->title;
		$TransferLog->save();

		return response()->json(['status' => 1, 'message' => '升级成功']);

	}


	//我的vip
	public function vip(){

		$vipTags = ['新手体验','小试身手','修车学徒','修车师父','初阶司机','资深司机','专业司机','老司机','老司机'];

		$ServiceConfig = \App\ServiceConfig::where('enable', 'on')->orderBy('id','asc')->get()->toArray();

		return view('user.vip',['ServiceConfig' => $ServiceConfig ,'vipTags' => $vipTags]);
	}
	//會員充值
	public function recharge(){
		$RechargeConfig = \App\RechargeConfig::where('enable', 'on')->orderBy('updated_at','desc')->get()->toArray();
		//$RechargeConfig = \App\RechargeConfig::where('enable', 'on')->orderBy('updated_at','dec')->get()->toArray();
		return view('user.recharge',['RechargeConfig' => $RechargeConfig]);
		//return view('user.recharge',['RechargeConfig' => $RechargeConfig]);
	}

	public function fbRecharge(){
		$RechargeConfig = \App\RechargeConfig::where('enable', 'on')->orderBy('updated_at','dsec')->get()->toArray();
		//$RechargeConfig = \App\RechargeConfig::where('enable', 'on')->orderBy('updated_at','dec')->get()->toArray();
		return view('user.fbRecharge',['RechargeConfig' => $RechargeConfig]);
	}


		/*
		大陸金流 ( PAY_TYPE=TY-CHINA )
		PD-CREDIT-CHINAPAY	銀聯信用卡及金融卡付款	人民幣 ( CNY )	-
		PD-CREDIT-CHINAPAY	銀聯信用卡及金融卡代收	人民幣 ( CNY )	-
		PD-CREDIT-CHINAPAY-TWD	銀聯信用卡及金融卡代收(台幣計算)	台幣 ( TWD )	-
		PD-EPOINT-QQPAY	QQ錢包支付	人民幣 ( CNY )	-
		PD-EPOINT-TENPAY-TWD	財付通(台幣)	台幣 ( TWD )	-
		PD-EPOINT-WECHAT	微信支付	人民幣 ( CNY )	-
		PD-EPOINT-WECHAT-TWD	微信支付(台幣)
		*/
	//成立充值訂單
	public function rechargeOrder(Request $request){

		//設定付款方式
		//$payment = ['PD-EPOINT-ALIPAY','PD-EPOINT-WECHAT','PD-CREDIT-CHINAPAY','PD-CREDIT-CHINAPAY-TWD'];

		$validator = Validator::make(
			$request->all(),
			[
				'rechargeId' => 'required|integer|between:1,100',
				'payment' => 'required|string',
			],
			[
			]
		);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors(['message' => $validator->messages()->first()]);			
		}

		if (in_array($request->payment,array_keys(config('av.payment'))) === false) {
			return redirect()->back()->withErrors(['message' => '錯誤的付款方式']);		
		}
		
		$RechargeConfig = \App\RechargeConfig::find($request->rechargeId);

		if (!$RechargeConfig) {
			return redirect()->back()->withErrors(['message' => '錯誤的充值金额']);				
		}

		$UserRecharge = new \App\UserRecharge;
		$UserRecharge->user_id = Auth::id();
		$UserRecharge->order_no = uniqid(date('ymdhis'));
		//$UserRecharge->amount = $RechargeConfig->amount;
		$UserRecharge->amount = ($request->payment == 'PD-CREDIT-CHINAPAY-TWD' ? $RechargeConfig->amount * 4 : $RechargeConfig->amount);
		$UserRecharge->currency = ($request->payment == 'PD-CREDIT-CHINAPAY-TWD' ? 'NT' : 'RMB');
		$UserRecharge->coins = $RechargeConfig->coins;
		$UserRecharge->service_id = null;
		$UserRecharge->payment = $request->payment;
		$UserRecharge->save();

		$Happypay = new \App\Payment\Happypay;
		$params = $Happypay->getParameters(array(
			'ORDER_ID' => $UserRecharge->order_no,
			'AMOUNT' => intval($UserRecharge->amount),
			'ORDER_ITEM' => $RechargeConfig->title,
			'PROD_ID' => $UserRecharge->payment,
			'SHOP_PARA' => Auth::id(),
			//'CURRENCY' => $currency,
			'CURRENCY' => ($request->payment == 'PD-CREDIT-CHINAPAY-TWD' ? 'TWD' : 'CNY'),
		));

		Log::info(json_encode($params));
		return view('vendor.happypay',['params' => $params]);
	}




	//nnex必付寶
	public function nnexRechargeOrder(Request $request){

		$validator = Validator::make(
			$request->all(),
			[
				'rechargeId' => 'required|integer|between:1,100',
				'payment' => 'required|string',
			],
			[
			]
		);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors(['message' => $validator->messages()->first()]);			
		}

		$RechargeConfig = \App\RechargeConfig::find($request->rechargeId);

		if (!$RechargeConfig) {
			return redirect()->back()->withErrors(['message' => '錯誤的充值金额']);
		}

		if($RechargeConfig->amount < 1 ) {
			return redirect()->back()->withErrors(['message' => '此渠道充值金额不得小於1']);
		}

		$UserRecharge = new \App\UserRecharge;

		$UserRecharge->user_id = Auth::id();
		$UserRecharge->order_no = 'E'.date("YmdHis").rand(100000,999999);
		$UserRecharge->amount = $RechargeConfig->amount;
		$UserRecharge->currency = 'RMB';
		$UserRecharge->coins = $RechargeConfig->coins;
		$UserRecharge->service_id = null;
		$UserRecharge->payment = $request->payment;
		$UserRecharge->save();

		$Nnexpay = new \App\Payment\Nnexpay;
		$params = $Nnexpay->getParameters(array(
			'ORDER_ID' => $UserRecharge->order_no,
			'AMOUNT' => intval($UserRecharge->amount),
			'TYPE' => $request->payment,
			'FINISH_URL' => url('/') . "/goto/vip",
			'CALLBACK_URL' => url('/'). "/api/nnexpay/Callback",
			// 'CALLBACK_URL' => "http://bifubao.twdio.com/api/bifubaopay/Callback",

		));

		Log::info(json_encode($params));
		return view('vendor.nnexpay',['params' => $params]);

	}



	//bf必付寶
	public function bfRechargeOrder(Request $request){
		$validator = Validator::make(
			$request->all(),
			[
				'rechargeId' => 'required|integer|between:1,100',
				'payment' => 'required|string',
			],
			[
			]
		);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors(['message' => $validator->messages()->first()]);			
		}

		//設定付款方式
		$payment = ['13','18'];

		if (in_array($request->payment,$payment) === false) {
			return redirect()->back()->withErrors(['message' => '錯誤的付款方式']);		
		}

		$RechargeConfig = \App\RechargeConfig::find($request->rechargeId);

		//dd($RechargeConfig->amount);

		if (!$RechargeConfig) {
			return redirect()->back()->withErrors(['message' => '錯誤的充值金额']);
		}

		if($RechargeConfig->amount < 100 && $request->payment == 18) {
			return redirect()->back()->withErrors(['message' => '此渠道充值金额不得小於100']);
		}

		if($RechargeConfig->amount < 50 && $request->payment == 13) {
			return redirect()->back()->withErrors(['message' => '此渠道充值金额不得小於50']);
		}

		$UserRecharge = new \App\UserRecharge;

		$UserRecharge->user_id = Auth::id();
		$UserRecharge->order_no = uniqid(date('ymdhis'));
		//$UserRecharge->amount = $RechargeConfig->amount;
		$UserRecharge->amount = $RechargeConfig->amount;
		$UserRecharge->currency = 'RMB';
		$UserRecharge->coins = $RechargeConfig->coins;
		$UserRecharge->service_id = null;
		$UserRecharge->payment = $request->payment;
		$UserRecharge->save();
		
		$BiFubaopay = new \App\Payment\BiFubaopay;
		$params = $BiFubaopay->getParameters(array(
			'ORDER_ID' => $UserRecharge->order_no,
			'AMOUNT' => intval($UserRecharge->amount),
			'TYPE' => $request->payment,
			'FINISH_URL' => url('/') . "/goto/vip",
			//'CALLBACK_URL' => "https://www.avddav.com/api/bifubaopay/Callback",
			'CALLBACK_URL' => "http://bifubao.twdio.com/api/bifubaopay/Callback",
			//'CALLBACK_URL' => url('/') . "/api/bifubaopay/Callback",
		));

		Log::info(json_encode($params));
		return view('vendor.bfpay',['params' => $params]);

	}

	// 使用肥寶支付
	public function fbRechargeOrder(Request $request){

		$validator = Validator::make(
			$request->all(),
			[
				'fbrechargeId' => 'required|integer|between:1,100',
				'fbpayment' => 'required|string',
			],
			[
			]
		);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors(['message' => $validator->messages()->first()]);			
		}

		if($request->fbpayment == 'PD-CREDIT-CHINAPAY-TWD'){
			if (in_array($request->fbpayment,array_keys(config('av.payment'))) === false) {
				return redirect()->back()->withErrors(['message' => '錯誤的付款方式']);		
			}
		}
		else{
			if (in_array($request->fbpayment,array_keys(config('av.fbPayment'))) === false) {
				return redirect()->back()->withErrors(['message' => '錯誤的付款方式']);		
			}
		}

		$RechargeConfig = \App\RechargeConfig::find($request->fbrechargeId);

		if (!$RechargeConfig) {
			return redirect()->back()->withErrors(['message' => '錯誤的充值金额']);
		}

		if($RechargeConfig->amount < 100) {
			return redirect()->back()->withErrors(['message' => '此渠道充值金额不得小於100']);
		}

		//dd($RechargeConfig->amount);

		$UserRecharge = new \App\UserRecharge;
		$UserRecharge->user_id = Auth::id();
		$UserRecharge->order_no = uniqid(date('ymdhis'));
		$UserRecharge->amount = $RechargeConfig->amount;
		$UserRecharge->currency = 'RMB';
		$UserRecharge->coins = $RechargeConfig->coins;
		$UserRecharge->service_id = null;
		$UserRecharge->payment = $request->fbpayment;
		$UserRecharge->save();

		$Feibaopay = new \App\Payment\Feibaopay;
		$userIP = User::where('id',  Auth::id())->first();
		$Agent = new \Jenssegers\Agent\Agent;

		$params = $Feibaopay->getParameters(array(
			'amount' => intval($UserRecharge->amount),
			'device' => $Agent->isMobile() ? 'mobile': 'desktop',
			'gateway' => $UserRecharge->payment,
			'merchant_order_num' => $UserRecharge->order_no,
			'merchant_order_remark' => $RechargeConfig->title,
			'merchant_order_time' => $this->microtime_float(),
			'uid' => Auth::id(),
			'user_ip' => $userIP->source_ip ? $userIP->source_ip : '127.0.0.1',
			//'callback_url' => 'http://avtiger.1778mao.com:8091/api/feibaopay/Callback',
			'callback_url' => url('/') . "/api/feibaopay/Callback",					
		));

		$cmd = "curl -X POST -H 'Content-Type: application/json' -d '". json_encode($params) . "' https://api.feibaopay.com/v3/deposit";
		
		$result = json_decode(shell_exec($cmd));

		Log::info(json_encode( ['deposit', $result] ));

		if ( ($result->code == 0) && ($result->merchant_slug == "qiaohuav") ) {
			$order_info = $Feibaopay->verifyOrders( $result->order );
			return view('vendor.feibaopay',['navigate_url' => $order_info['navigate_url'] ]);
		}
		else{
			return redirect()->back()->withErrors(['message' => '錯誤的支付渠道']);
		}

	}

	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	//交易記錄
	public function transfer() {

		$TransferLog = \App\TransferLog::where('user_id', Auth::id())->orderBy('id','desc')->paginate(10);

        return view('user.transfer',compact('TransferLog'));
   //     return view('user.transfer',[]);

	}

	//個人設置
	public function personal(){
		return view('user.personal',[]);
	}

	//歡看記錄
	public function watches(){

		$AvUserWatches = \App\AvUserWatch::where('user_id', Auth::id())->where('updated_at','>', date('Y-m-d 00:00:00',strtotime("-7 days")))->orderBy('updated_at','desc')->pluck('video_id')->toArray();
		$avkeys = [];
		if($AvUserWatches) {
			$ids = implode(',',$AvUserWatches);
			$avkeys = \App\AvVideo::where('enable','on')->whereIn('id', $AvUserWatches)->orderByRaw(DB::raw("FIELD(id, $ids)"))->pluck('avkey','id')->toArray();
		}
		$links = $this->paginate($avkeys, 20)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}
		return view('user.watches',['AvVideos' => $AvVideos, 'links' => $links]);
	}
	//我的影片
	public function videos(){

		$AvUserVideos = \App\AvUserVideo::where('user_id', Auth::id())->orderBy('created_at','desc')->pluck('video_id')->toArray();
		$avkeys = [];
		if($AvUserVideos) {
			$ids = implode(',',$AvUserVideos);
			$avkeys = \App\AvVideo::where('enable','on')->whereIn('id', $AvUserVideos)->orderByRaw(DB::raw("FIELD(id, $ids)"))->pluck('avkey','id')->toArray();
		}
		$links = $this->paginate($avkeys, 20)->links('layouts/paginator');
		$AvVideos = array();
		foreach ($avkeys as $avkey){
			$AvVideos[] = $this->getVideo($avkey);
		}
		return view('user.videos',['AvVideos' => $AvVideos, 'links' => $links]);
	}
	//我的女優
	public function actors (){

		$AvUserActors = \App\AvUserActor::where('user_id', Auth::id())->orderBy('updated_at','desc')->pluck('actor_id')->toArray();
		if (!$AvUserActors) $AvUserActors = [];
		$links = $this->paginate($AvUserActors, 30)->links('layouts/paginator');
		$AvActors = array();
		foreach ($AvUserActors as $actor_id){
			$AvActors[] = $this->getActor($actor_id);
		}
		return view('user.actors',['AvActors' => $AvActors, 'links' => $links]);
	
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




}
