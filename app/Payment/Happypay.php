<?php

namespace App\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
//中華國際
class Happypay
{
	/*
		後台 http://mng.55168957.com:30080/queplat/
		帳號 m010641 / O7H2CwakLh
	*/
	private $SHOP_ID = 'M010641'; //商家ID
	private $SHOP_TRUST_CODE = 'w3l5pTglpn'; //商家信任碼
	private $SYS_TRUST_CODE = 'yjJLtPu1z4'; //系統信任碼

    public function __construct()
    {
	
	}

	//Receive01
	//
	public function order(Request $request){
		/*
		RES_CODE 
		0 成功
		20001 接收參數錯誤
		20002 比對 CHECK_CODE 錯誤
		20003 資料庫連接失敗
		20004 資料庫寫入失敗
		20099 其他錯誤
		*/

		Log::info(json_encode($request->all()));
		$parameters = [
			//'USER_ID' => Auth::id(),
			'USER_ID' => isset($request->SHOP_PARA) ? $request->SHOP_PARA : '1',
			'RES_CODE' => 0,
			'SHOP_PARA' => isset($request->SHOP_PARA) ? urlencode($request->SHOP_PARA) : '',
			'RET_URL' => '',
		];
		$CHECK_CODE = md5($this->SYS_TRUST_CODE . '#' . $request->SHOP_ID . '#' . $request->ORDER_ID . '#' . $request->AMOUNT . '#' . $request->SESS_ID . '#' . $request->PROD_ID . '#' . $this->SHOP_TRUST_CODE);

		if ($CHECK_CODE != $request->CHECK_CODE) {
			$parameters['RES_CODE'] = 20002;
		}

		Log::info(json_encode($parameters));
		return response(http_build_query($parameters), 200);

	}
	//Receive02
	//http://vpn.twdio.com/api/payment/callback
	public function callback(Request $request){
		/*
		RES_CODE
		0 成功
		20201 接收參數錯誤
		20202 資料庫連接失敗
		20203 資料庫寫入失敗
		20290 資料已接收過
		20299 其他錯誤		
		*/

		Log::info(json_encode($request->all()));
		$parameters = [
			'RES_CODE' => 0,
		];
		$CHECK_CODE = md5($this->SYS_TRUST_CODE . '#' . $request->SHOP_ID . '#' . $request->ORDER_ID . '#' . $request->AMOUNT . '#' . $request->SESS_ID . '#' . $request->PROD_ID . '#' . $request->USER_ID . '#' . $this->SHOP_TRUST_CODE);
		if ($CHECK_CODE != $request->CHECK_CODE) {
			$parameters['RES_CODE'] = 20299; //接收參數錯誤
			$parameters['MSG'] = 'CHECK_CODE_ERRORS'; 
		}
		else {

			if ($request->TRADE_CODE == 0){

				$UserRecharge = \App\UserRecharge::where(['order_no' => $request->ORDER_ID, 'status' => 0])->first();

				if ($UserRecharge) {
					$UserRecharge->status = 1;
					$UserRecharge->update();

					$user = \App\User::find($UserRecharge->user_id);
					$oldCoins = $user->coins;
					$user->increment('coins', $UserRecharge->coins);


					$TransferLog = new \App\TransferLog;
					$TransferLog->order_no = $UserRecharge->order_no;
					$TransferLog->user_id = $UserRecharge->user_id;
					$TransferLog->coins = $UserRecharge->coins;
					$TransferLog->user_coins = $user->coins;
					$TransferLog->type = 1;
					$TransferLog->parent_id = $UserRecharge->id;
					$TransferLog->memo = '会员充值';
					$json = [
						'coins' => ['from' => $oldCoins, 'to' => $user->coins],
						'transfer' => [
							'amount' => $UserRecharge->amount,
							'currency' => $UserRecharge->currency,
							'payment' => $UserRecharge->payment,
						]
					];

					$TransferLog->json = $json;
					$TransferLog->logs = $request->all();
					$TransferLog->save();

				}
				else {
					$parameters['RES_CODE'] = 20299;
					$parameters['MSG'] = 'ORDER_ID_ALREADY_TRASNFER'; 
				}

			}
			else {
				$parameters['RES_CODE'] = $request->TRADE_CODE;
			}
		}

		Log::info(json_encode($parameters));
		return response(http_build_query($parameters), 200);
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
	public function getParameters($params)
	{
		//$ORDER_ITEM =  urlencode(mb_convert_encoding($string,"utf-8","big5"));
		$parameters = [
			'SHOP_ID' => $this->SHOP_ID,
			'ORDER_ID' => $params['ORDER_ID'],
			'ORDER_ITEM' => isset($params['ORDER_ITEM']) && $params['ORDER_ITEM'] ? urlencode($params['ORDER_ITEM']) : '',
			'AMOUNT' => $params['AMOUNT'],
			'CURRENCY' => isset($params['CURRENCY']) && $params['CURRENCY'] ? $params['CURRENCY'] : 'TWD',
			'PROD_ID' => isset($params['PROD_ID']) && $params['PROD_ID'] ? $params['PROD_ID'] : '', //金流代碼 => http://api.55168957.com/info/payprodlist.php
			'SHOP_PARA' => isset($params['SHOP_PARA']) && $params['SHOP_PARA'] ? urlencode($params['SHOP_PARA']) : '',
		];
		$parameters['CHECK_CODE'] = md5($this->SYS_TRUST_CODE . '#' . $parameters['SHOP_ID'] . '#' . $parameters['ORDER_ID'] . '#' . $parameters['AMOUNT'] . '#' . $this->SHOP_TRUST_CODE);

		return $parameters;

	}


}
