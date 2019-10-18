<?php

namespace App\Admin\Controllers;
//namespace Carbon;

use App\ReportRecharge;
use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
//use Encore\Admin\Widgets\Tab;


//use Carbon\Carbon;

class ReportRechargeController extends Controller
{
    private $start_date;
	private $end_date;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {

            // 確認搜尋區間
            $this->start_date = $request->input('start_date');
			$this->end_date = $request->input('end_date');

			if (!$this->start_date)
			{
				//$this->start_date = date('Y/m/d',strtotime('now'));
				$this->start_date = date('Y-m-01');
			}
				
			if (!$this->end_date) $this->end_date = date('Y/m/d',strtotime('now'));

            $content->header('充值金額報表');
			$content->description( date_format( new \DateTime(),"Y/m/d H:i:s") . ' 以巧虎AV為主' );

            // 搜尋用的表單
            $content->row(view('admin.report.rechargeDaily')
                ->with('start_date', $this->start_date)
				->with('end_date', $this->end_date)
                ->render());

            $content->row($this->customGridNew());
        });
    }


    protected function customGridNew()
    {
        $headers = ["日期", "當日儲值總額", "當日不重複儲值人數", "當日總儲值次數", "當日單次最高儲值金額", "當日儲值總額最高帳號"];

		$sumHeaders = ["", "總金額總和", "不重複儲值人數總和", "總儲值次數總和", "總點數 ( 總次數*面額 )"];
		
		$items = ReportRecharge::getNewRMBItem($this->start_date, $this->end_date);

		if($items)
		{
			/*
			$roughItems = $items
				->get()
				->groupBy(function ($val) {
					return [$val->currency, \Carbon\Carbon::parse($val->updated_at)->format('y/m/d')];
				})
				->map(function ($value, $key) {
					$value['sumAccounts'] = $value->unique('user_id')->count();
					$value['sumUsers'] = $value->unique('id')->count()-1;
					$value['sumAmount'] = $value->sum('amount');
					$value['maxAmount'] = $value->max('amount');
					$maxAmountUser = $value->where('amount', $value->max('amount') )->first()->only('user_id');
					$value['currency'] = $value->first()->only('currency');

					$value['maxAmountUserName'] = $maxAmountUser ? User::where('id', $maxAmountUser)->first()->only(['name']) : null;

					return $value;
				})
				->toArray();
			*/

			/*
			$startDate = date('Y-m-d 00:00:00',strtotime($this->start_date));
			$endDate = date('Y-m-d 23:59:59',strtotime($this->end_date));
			$items = ReportRecharge::selectRaw('DATE(updated_at) as date, sum(amount) amount,currency,max(amount) as max_amount,count(DISTINCT(user_id)) as distinct_count,count(user_id) as count')
				->where('status', 1)
				->whereBetween('updated_at', [$startDate,$endDate])
				->groupBy("date", "currency")
				->get()->toArray();
			dd($items);
			*/



			//dd($items->get());
			$roughItems = $items
				->get()
				->groupBy([function ($val) {
					return \Carbon\Carbon::parse($val->updated_at)->format('y/m/d');
				}, 'currency'], $preserveKeys = true)
				->map(function ($value, $key) {
					//if($key == "19/05/21") {
					foreach($value as $key1 => $val)
					{
						$value[$key1]['sumAccounts'] = $value[$key1]->unique('user_id')->count();
						$value[$key1]['sumUsers'] = $value[$key1]->unique('id')->count()-1;
						$value[$key1]['sumAmount'] = $value[$key1]->sum('amount');
						$value[$key1]['maxAmount'] = $value[$key1]->max('amount');
						$value[$key1]['currency'] = $value[$key1]->first()->only('currency');
						$maxAmountUser = $value[$key1]->where('amount', $value[$key1]->max('amount') )->first()->only('user_id');
						$value[$key1]['maxAmountUserName'] = $maxAmountUser ? User::where('id', $maxAmountUser)->first()->only(['name']) : null;
					}
					//}

					//$value['sumAccounts'] = $value->unique('user_id')->count();
					//$value['sumUsers'] = $value->unique('id')->count()-1;
					//$value['sumAmount'] = $value->sum('amount');
					//$value['maxAmount'] = $value->max('amount');
					//$maxAmountUser = $value->where('amount', $value->max('amount') )->first()->only('user_id');
					//$value['currency'] = $value->first()->only('currency');
					//$value['maxAmountUserName'] = $maxAmountUser ? User::where('id', $maxAmountUser)->first()->only(['name']) : null;

					return $value;
				})
				
				->toArray();

			$sumItems = $items
				->get()
				->groupBy([function ($val) {
					return $val->amount;
				}, 'currency'], $preserveKeys = true)
				->map(function ($value, $key) {

					foreach($value as $key1 => $val)
					{
						$value[$key1]['sumAccounts'] = $value[$key1]->unique('user_id')->count();
						$value[$key1]['sumUsers'] = $value[$key1]->unique('id')->count()-1;
						$value[$key1]['sumAmount'] = $value[$key1]->sum('amount');
						$value[$key1]['sumCoins'] = $value[$key1]->sum('coins');
						$value[$key1]['currency'] = key(reset($value));

						//$a[key(reset($value))] += $value[$key1]->sum('amount');
					}

					return $value;
				})
				->toArray();

			$totalAmounts = 0;
			$tmpAmount = 0;
			$totalItems['totalAmountsNT'] = 0;
			foreach($sumItems as $sumItem1)
			{
				$tmpAmount = $sumItem1[key($sumItem1)]["sumAmount"];
				//dd(key($sumItem1));
				if( key($sumItem1) != "RMB" )
				{
					$totalItems['totalAmountsNT'] += $tmpAmount;
					//$tmpAmount = $tmpAmount / 4;
				}
				else
				{
					$totalAmounts = $totalAmounts + $tmpAmount;
				}
			}

			$totalItems['totalAmounts'] = $totalAmounts;  //總金額總和
			//$totalItems['totalAmounts'] = $items->get()->sum('amount');  //總金額總和
			$totalItems['totalCoins'] = $items->get()->sum('coins');  //總點數 ( 總次數*面額 )
			$totalItems['totalAccounts'] = $items->get()->unique('user_id')->count();  //不重複儲值人數總和
			$totalItems['totalUsers'] = $items->get()->count('id');  //總金額總和

			//dd( $items->get()->toArray()[0] );

		}

		//dd($roughItems);
		
		$view = view('admin.report.cellsRecharge')
            ->with('headers', $headers)
			->with('sumHeaders', $sumHeaders)
            ->with('roughItems', $roughItems)
			->with('sumItems', $sumItems)
			->with('totalItems', $totalItems)
			//->with('currency', $currency)
			->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;
		
    }

    protected function customGrid()
    {
        //$headers = ["人數 ( 帳號數 )", "總次數 ( 不分帳號 )", "總金額 ( 人數*總次數 )", "總點數 ( 總次數*面額 )"];
		$headers = ["當月不重複儲值人數", "當月總儲值次數", "當月除值總額", "總點數 ( 總次數*面額 )"];

		
        $rows = [];

		$items = ReportRecharge::getNewRMBItem($this->start_date, $this->end_date);

		if($items)
		{
			$totalSumByAllAccounts = $items->selectRaw('count(user_id) as totalAccount, user_id')
							->get()->toArray()[0]['totalAccount'];

			$totalSumItems = $items->selectRaw('sum(amount) as totalAmount, amount')
							->selectRaw('sum(coins) as totalCoins, coins')
							->selectRaw('sum(user_id) as totalAccount, user_id')
							->selectRaw('sum(id) as totalUsers, id')
							->get()
							->toArray();

			$contentItems = $items->selectRaw('sum(amount) as groupAmount, amount')
							->selectRaw('sum(coins) as groupCoins, coins')
							->distinct('user_id')
							->selectRaw('  count(id) as groupUsers, id')
							->groupBy('amount')
							->get()
							->toArray();
		}

		$items = ReportRecharge::getNewRMBItem($this->start_date, $this->end_date);
		if($items)
		{
			$accountItems = $items->get()->groupBy('amount');

			$accountItems =  $accountItems->map(function ($item) {
				return $item->unique('user_id')->count();
			});
			$accountItems =  $accountItems->toArray();

			$totalSumByAllUsers = array_sum($accountItems);
		}
		
		$keyCount = 0;

		foreach($accountItems as $accountItem)
		{
			$contentItems[$keyCount]['groupAccount'] = $accountItem;
			$keyCount++;
		}

		$view = view('admin.report.cellsRecharge')
            ->with('headers', $headers)
            ->with('contentItems', $contentItems)
			->with('totalSumByAllAccounts', $totalSumByAllAccounts)
			->with('totalSumByAllUsers', $totalSumByAllUsers)
			->with('totalSumItems', $totalSumItems)
            ->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;

    }





}
