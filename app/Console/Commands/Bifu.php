<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
class Bifu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bifu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '必付寶查檢成單';


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
		//$UserRecharge = \App\UserRecharge::where('status',0)->whereIn('payment',[18])->where('created_at', '>=' , date('Y-m-d H:i:s', time() - 1200) )->get();
		$UserRecharge = \App\UserRecharge::where('status',0)->whereIn('payment',[13,18])->where('created_at', '>=' , date('Y-m-d H:i:s', time() - 1200) )->get();

		$BiFubaopay = new \App\Payment\BiFubaopay;

		if ($UserRecharge) foreach ($UserRecharge as $row){


			if($row->payment == 13){
				$BiFubaopay->wechat();
			}
			else if ($row->payment == 18){
				$BiFubaopay->alipay();
			}

			$result = $BiFubaopay->checkOrder($row->order_no);
			if (isset($result['STATUS']) && $result['STATUS'] == 1) {
				Log::info(json_encode(['cronjob' => $result]));
				print_r($result);

				$row->status = 1;
				$row->update();

				$user = \App\User::find($row->user_id);
				$oldCoins = $user->coins;
				$user->increment('coins', $row->coins);


				$TransferLog = new \App\TransferLog;
				$TransferLog->order_no = $row->order_no;
				$TransferLog->user_id = $row->user_id;
				$TransferLog->coins = $row->coins;
				$TransferLog->user_coins = $user->coins;
				$TransferLog->type = 1;
				$TransferLog->parent_id = $row->id;
				$TransferLog->memo = '会员充值';
				$json = [
					'coins' => ['from' => $oldCoins, 'to' => $user->coins],
					'transfer' => [
						'amount' => $row->amount,
						'currency' => $row->currency,
						'payment' => isset(config('av.payment')[$row->payment])? config('av.payment')[$row->payment] : $row->payment,
					]
				];

				$TransferLog->json = $json;
				$TransferLog->logs = $result;
				$TransferLog->save();


			
			
			}

		}


    }

	
}
