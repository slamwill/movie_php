<?php

namespace App\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
//必付寶
class BiFubaopay
{
	/*
		登入連結: https://merchant.bfbaopay.com/#/client/index
		文檔連結: https://merchant.bfbaopay.com/#/client/document/download
		帳號 C000000001203 / 7536951
	*/

	private $MERCHANT_ID = "";
	private $MD5KEY = "";

	private $MERCHANT_ID_WECHAT = "SP001203O0";  //微信使用
	private $MD5KEY_WECHAT = "1f8d114bd85fa41e";  //微信使用

	private $MERCHANT_ID_ALIPAY = "SP001203PENSTONE0";  //微信使用
	private $MD5KEY_ALIPAY = "d99a28bbc3684d83";  //微信使用

    public function __construct()
    {
	}

	public function callback (Request $request){
		if($request->TYPE == 13){
			$this->wechat();
		}
		else if ($request->TYPE == 18){
			$this->alipay();
		}
		else{
			Log::info(json_encode(['error_payment_type' => $request->all()]));
			return response('error_payment_type', 200);
		}

		Log::info(json_encode(['callback' => $request->all()]));

		$data = $request->all();
		unset($data['SIGNED_MSG']);

		ksort($data);

		$paramSrc = urldecode(http_build_query($data));
		$sign =  md5($paramSrc.$this->MD5KEY);
		//$sign = md5($paramSrc.$this->MD5KEY);

		if ($sign != $request->SIGNED_MSG) {
			Log::info(json_encode(['sign_error' => $sign]));
			return response('success', 200);
		}
		$order = $this->checkOrder($request->TRAN_CODE, $request->TYPE);

		if ($request->STATUS == 1 && $order['STATUS'] == 1){

			$UserRecharge = \App\UserRecharge::where(['order_no' => $request->TRAN_CODE, 'status' => 0])->first();

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
						'payment' => isset(config('av.payment')[$UserRecharge->payment])? config('av.payment')[$UserRecharge->payment] : $UserRecharge->payment,
					]
				];

				$TransferLog->json = $json;
				$TransferLog->logs = $request->all();
				$TransferLog->save();

			}


		}

		
		
		return response('success', 200);
	}
	public function getParameters($params)
	{
		if($params['TYPE'] == 13){
			$this->wechat();
		}
		else if ($params['TYPE'] == 18){
			$this->alipay();
		}
				
		$parameters = [
			'MERCHANT_ID' => $this->MERCHANT_ID,
			'TRAN_CODE' => $params['ORDER_ID'],
			'REMARK' => '虚拟点数',
			'TRAN_AMT' => $params['AMOUNT'] * 100,
			'TYPE' => $params['TYPE'],
			'BANK_ACCOUNT_NO' => '',
			'NO_URL' => $params['CALLBACK_URL'],
			'RET_URL' => $params['FINISH_URL'],
			'SUBMIT_TIME' => date('YmdHis'),
			'VERSION' => '1',
		];

		ksort($parameters);

		$paramSrc = urldecode(http_build_query($parameters));
		//$sign = md5($paramSrc.$this->MD5KEY);
		$sign = md5($paramSrc.$this->MD5KEY);

		//if( $params['TYPE'] == 18 ) $sign = md5($paramSrc.$this->MD5KEY);
		//else if( $params['TYPE'] == 13 ) $sign = md5($paramSrc.$this->MD5KEY_H5);

		$parameters['SIGNED_MSG'] =  $sign;

		return $parameters;

	}



	public function wechat(){

		$this->MERCHANT_ID = $this->MERCHANT_ID_WECHAT;
		$this->MD5KEY = $this->MD5KEY_WECHAT;

	}
	public function alipay(){

		$this->MERCHANT_ID = $this->MERCHANT_ID_ALIPAY;
		$this->MD5KEY = $this->MD5KEY_ALIPAY;

	}

	public function checkOrder($ORDER_ID)
	{		

		$data = [
			'MERCHANT_ID' => $this->MERCHANT_ID,
			'TRAN_CODE' => $ORDER_ID,
			'VERSION' => '1',
		];
		ksort($data);

		$paramSrc = urldecode(http_build_query($data));
		$sign = md5($paramSrc.$this->MD5KEY);
		//$sign = md5($paramSrc.$this->MD5KEY);

		$data['SIGNED_MSG'] =  $sign;


		$cmd = "curl 'https://api.bfbaopay.com/bifubao-gateway/back-pay/qr-pay-query?".http_build_query($data)."'";

		$order = json_decode(shell_exec($cmd), true);
		return $order;
	
	}








}
