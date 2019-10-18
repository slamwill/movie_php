<?php

namespace App\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
//必付寶
class Nnexpay
{
	/*
		商户登录链接：http://all.nn-ex.com/Home_Index_userLogin.html
		开发文档链接：http://all.nn-ex.com/Home_Index_document.html
	*/

	private $pay_memberid = "190655106";
	private $pay_md5sign = "f7v0rsg481dxgbzhcb1dzwenodhs1wk7";

    public function __construct()
    {
	}

	public function getParameters($params)
	{
		//dd('sssssssss');
		//dd($params);
				
		$parameters = [
			"pay_memberid" => $this->pay_memberid,
			"pay_orderid" => $params['ORDER_ID'],
			"pay_amount" => $params['AMOUNT'],
			"pay_applydate" => date("Y-m-d H:i:s"),
			"pay_bankcode" => $params['TYPE'],
			"pay_notifyurl" => $params['FINISH_URL'],
			"pay_callbackurl" => $params['CALLBACK_URL'],
		];


		ksort($parameters);
		$md5str = "";

		foreach ($parameters as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}

		$sign = strtoupper(md5($md5str . "key=" . $this->pay_md5sign));
		$parameters["pay_md5sign"] = $sign;
		$parameters['pay_attach'] = "1234|456";
		$parameters['pay_productname'] ='团购商品';


		return $parameters;

	}

	public function callback (Request $request){

		Log::info(json_encode(['Nnex_callback_1' => $request->all() ]));
		//return response('error_payment_typeaaaaa', 200);
		
		$returnArray = array( // 返回字段
            "memberid" => $request->memberid, // 商户ID
            "orderid" =>  $request->orderid, // 订单号
            "amount" =>  $request->amount, // 交易金额
            "datetime" =>  $request->datetime, // 交易时间
            "transaction_id" =>  $request->transaction_id, // 流水号
            "returncode" => $request->returncode
        );

		Log::info(json_encode(['Nnex_callback_2' => $returnArray ]));
		
        $md5key = $this->pay_md5sign; //商户APIKEY,商户后台API管理获取
        ksort($returnArray);
        reset($returnArray);
        $md5str = "";

        foreach ($returnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }

        $sign = strtoupper(md5($md5str . "key=" . $md5key)); 
        // if($sign == $_REQUEST["sign"]) {
		if($sign == $request->sign ) {
			Log::info(json_encode(['Nnex_sign_correct' => $sign]));
            // if($_REQUEST["returncode"] == "00") {
			if( $request->returncode == "00" ) {

				// $UserRecharge = \App\UserRecharge::where(['order_no' => $request->TRAN_CODE, 'status' => 0])->first();
				$UserRecharge = \App\UserRecharge::where(['order_no' => $request->orderid, 'status' => 0])->first();

				if($UserRecharge) {
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
			    // $str = "交易成功！订单号：".$_REQUEST["orderid"];
			    // exit($str);

			    return response('success', 200);

            }
        }
		else{
			Log::info(json_encode(['Nnex_sign_error' => $sign]));
			return response('sign_error', 200);
		}
	}

	public function checkOrder($ORDER_ID)
	{
		$data = [
			'pay_memberid' => $this->pay_memberid,
			'pay_orderid' => $ORDER_ID,
			// 'pay_md5sign' => $this->pay_md5sign,
		];

		ksort($data);
		$md5str = "";
		foreach ($data as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}

		$sign = strtoupper(md5($md5str . "key=" . $this->pay_md5sign));
		$data["pay_md5sign"] = $sign;

		$cmd = "curl 'http://all.nn-ex.com/Pay_Trade_query.html?".http_build_query($data)."'";
		$order = json_decode(shell_exec($cmd), true);

		// var_dump($order);
		// return $order;

		return $order;
	}

	public function wechat(){

		$this->MERCHANT_ID = $this->MERCHANT_ID_WECHAT;
		$this->MD5KEY = $this->MD5KEY_WECHAT;

	}
	public function alipay(){

		$this->MERCHANT_ID = $this->MERCHANT_ID_ALIPAY;
		$this->MD5KEY = $this->MD5KEY_ALIPAY;

	}

}
