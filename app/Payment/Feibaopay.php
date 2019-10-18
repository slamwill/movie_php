<?php

namespace App\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
//中華國際
class Feibaopay
{

	/*
		登入連結: https://www.feibaopay.com
		文檔連結: https://www.feibaopay.com/api_cn.html
		帳號 admin / admin
	*/
	private $hash_key = "d75554d5fe8c0e19fb5046230f6dc1cc"; // 此处填写商号的hash_key
	private $hash_iv = "be4dabbd638aed79";   // 此处填写商号的hash_iv
	private $merchant_slug = "qiaohuav";

    public function __construct()
    {
	
	}

	/*
	大陸金流 ( PAY_TYPE=TY-CHINA )
	PD-EPOINT-WECHAT	微信支付	人民幣 ( CNY )	-
	PD-EPOINT-WECHAT-TWD	微信支付(台幣)
	*/
	public function getParameters($parameters)
	{
		ksort($parameters);
		$parameters['sign'] = sha1(json_encode($parameters)); // 做签

		//$returndata['merchant_slug'] = 'qiaohuav'; // 商号帐号
		$returndata['merchant_slug'] = $this->merchant_slug; // 商号帐号
		$returndata['data'] = $this->encryption($this->hash_key, $this->hash_iv, json_encode($parameters)); // 带签一起加密

		return $returndata;
	}

	public function encryption($hash_key, $hash_iv, $plaintext)
	{
		$cipher = "AES-256-CBC";
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $hash_key, OPENSSL_RAW_DATA, $hash_iv);
		return base64_encode( $ciphertext_raw );
	}


	public function verifyOrders($order)
	{
		$order_str = $this->decryption($this->hash_key, $this->hash_iv, $order);
		$order_info = json_decode($order_str, true);
		$order_sign = $order_info["sign"];
		unset($order_info["sign"]);
		ksort($order_info);

		if (sha1(json_encode($order_info, JSON_UNESCAPED_SLASHES)) != $order_sign){
			return "资料异常";
		} else {
			// 验证成功，是自己人。执行自己的业务逻辑：加余额，订单付款成功，装备购买成功等等。
			return $order_info;
			//return "success";
		}
	}

	public function decryption($hash_key, $hash_iv, $ciphertext_raw)
	{
		$cipher = "AES-256-CBC";
		$plaintext = openssl_decrypt(base64_decode($ciphertext_raw), $cipher, $hash_key, OPENSSL_RAW_DATA, $hash_iv);
		return $plaintext;
	}


	/*
	回调参数范例:
	{
		"code": 0,
		"msg": "ok",
		"action": "deposit",
		"merchant_slug": "merchant_demo",
		"merchant_order_num": "d8a1223deed6ee05ae72d19ef38195f9",
		"action": "deposit",
		"order": "u4HPZ2MO......"
	}
	*/
	public function callback(Request $request){
		//echo time();
		//echo "999999999";
		//dd('222222222');
		Log::info(json_encode($request->all() ));

		if($request->order)
		{
			$order_str = $this->decryption($this->hash_key, $this->hash_iv, $request->order);
			$order_info = json_decode($order_str, true);
			Log::info(json_encode( ['callback解碼', $order_info] ));
			$merchant_order_num = $order_info["merchant_order_num"];
			$order_sign = $order_info["sign"];
			unset($order_info["sign"]);
			ksort($order_info);

			if (sha1(json_encode($order_info, JSON_UNESCAPED_SLASHES)) != $order_sign){
				return "资料异常";
			} else {
				// 验证成功，是自己人。执行自己的业务逻辑：加余额，订单付款成功，装备购买成功等等。
				$res = $this->checkOrder($merchant_order_num);

				if($res == "success")
				{
					// update UserRecharge
					$UserRecharge = \App\UserRecharge::where(['order_no' => $merchant_order_num, 'status' => 0])->first();

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

						//dd($res);
						//return $res;

						return "success";
					}
					else {
						Log::info(json_encode( ['注單異常(已充值)', $merchant_order_num] ));
						return "资料异常";
					}
				}
				else
				{
					return "资料异常";
				}
			}
		}
		else
		{
			return "资料异常";
		}
	}


	public function checkOrder($merchant_order_num)
	{		
		$data = json_encode(array(
			//'merchant_slug' => 'qiaohuav',
			'merchant_slug' => $this->merchant_slug,
			'merchant_order_num' => $merchant_order_num,
		));

		//$returndata['merchant_slug'] = 'qiaohuav'; // 商号帐号
		$returndata['merchant_slug'] = $this->merchant_slug; // 商号帐号
		$returndata['data'] = $this->encryption($this->hash_key, $this->hash_iv, $data); // 带签一起加密

		$cmd = "curl -X POST -H 'Content-Type: application/json' -d '". json_encode($returndata) . "' https://api.feibaopay.com/v3/check";
		$order = json_decode(shell_exec($cmd), true)['order'];

		if($order)
		{
			$order_str = $this->decryption($this->hash_key, $this->hash_iv, $order);
			$order_info = json_decode($order_str, true);
			Log::info(json_encode( ['資料反查', $order_info] ));
			$order_sign = $order_info["sign"];

			$order_status = $order_info['status'];
			unset($order_info["sign"]);
			ksort($order_info);

			if (sha1(json_encode($order_info, JSON_UNESCAPED_SLASHES)) != $order_sign){
				return "资料异常";
			} else {
				return (( $order_status == "success" || $order_status == "success_done" ) ? "success" : "资料异常" );
			}
		}
		else
		{
			return "资料异常";
		}
	}








}
