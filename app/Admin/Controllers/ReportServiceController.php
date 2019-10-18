<?php

namespace App\Admin\Controllers;

use App\ReportService;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use App\User;
use DB;

class ReportServiceController extends Controller
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
				$this->start_date = date('Y-m-01');
			}
			//if (!$this->start_date) $this->start_date = date('Y/m/d',strtotime('now'));
			if (!$this->end_date) $this->end_date = date('Y/m/d',strtotime('now'));

            $content->header('方案設定報表');
			$content->description( date_format( new \DateTime(),"Y/m/d H:i:s") . ' 以巧虎AV為主' );

            // 搜尋用的表單
            $content->row(view('admin.report.rechargeDaily')
				->with('start_date', $this->start_date)
				->with('end_date', $this->end_date)
				->render());

            //$content->row($this->customGrid());
			//$content->row($this->customGridNew());
            $tab = new Tab();
            $tab->add('總表', $this->customGridTab1());
            $tab->add('耗點商品', $this->customGridTab2());
            $content->row($tab->render());
        });
    }





    protected function customGridTab1()
    {
		//dd('333');
		$headers = ["總次數 ( 不分帳號 )", "人數 ( 帳號數 )"];

		$items = ReportService::getCoinsItem($this->start_date, $this->end_date);

		//dd($items->get()->toArray());


		if($items)
		{
			$totalSumByAllAccounts = $items->selectRaw('count(user_id) as totalAccount, user_id')
							->get()->toArray()[0]['totalAccount'];

			$sumByItems = $items->selectRaw('count(user_id) as totalAccount, user_id')
							->groupBy('coins')
							->get()
							->toArray();
		}

		



		$items = ReportService::getCoinsItem($this->start_date, $this->end_date);
		if($items)
		{
			$contentItems = $items->get()->groupBy('coins');

			$contentItems =  $contentItems->map(function ($item) {
				return $item->unique('user_id')->count();
			});
			$contentItems =  $contentItems->toArray();

			$totalSumByAllUsers = array_sum($contentItems);

			$roughItems = $items->get()
				->groupBy(function ($val) {
					return $val->coins;
				})
				->map(function ($value, $key) {
					return $value;
				})
				->toArray();

			$tmpMemos = [];
			$tmpTotalMemos = [];
			//dd($roughItems);
			foreach($roughItems as $key1 => $roughItem)
			{
				foreach($roughItem as $key2 => $item)
				{
					$tmpMemos[] = $item["user_id"];			// 群組分類
					$tmpTotalMemos[] = $item["user_id"];		// 總和
				}
				// 群組分類
				$tmpUserName = \App\User::whereIn('id', $tmpMemos)->get()->pluck('name','id')->toArray();
				foreach($tmpMemos as $key3 => $tmpMemo)
				{
					$tmpMemos[$key3] = $tmpUserName[$tmpMemo];
				}
				$roughItems[$key1][$key1] = array_count_values($tmpMemos);
				arsort($roughItems[$key1][$key1]);
				unset($tmpMemos);
			}

			// 總和
			$tmpUserName = \App\User::whereIn('id', $tmpTotalMemos)->get()->pluck('name','id')->toArray();
			foreach($tmpTotalMemos as $key4 => $tmpTotalMemo)
			{
				$tmpTotalMemos[$key4] = $tmpUserName[$tmpTotalMemo];
			}
			$roughItems['totalMemos'] = array_count_values($tmpTotalMemos);
			arsort($roughItems['totalMemos']);
		}

		//dd($roughItems);

		$keyCount = 0;

		foreach($contentItems as $contentItem)
		{
			$sumByItems[$keyCount]['totalUsers'] = $contentItem;
			$keyCount++;
		}


		$items = ReportService::getDownloadsItem($this->start_date, $this->end_date)->get();

		if($items)
		{
			//下載總次數
			$totalDownloadsByAllUsers = $items->count();

			//下載總次數 by 帳號
			$totalDownloadsByAllAccounts =  $items->unique('user_id')->count();

			$totalDownloadsAmounts = abs($items->sum('coins'));
			//dd( $totalDownloadsAmounts );
		}

		$view = view('admin.report.cellsService')
            ->with('headers', $headers)
			->with('sumByItems', $sumByItems)
			->with('totalSumByAllAccounts', $totalSumByAllAccounts)
			->with('totalSumByAllUsers', $totalSumByAllUsers)
			->with('totalDownloadsByAllUsers', $totalDownloadsByAllUsers)
			->with('totalDownloadsByAllAccounts', $totalDownloadsByAllAccounts)
			->with('totalDownloadsAmounts', $totalDownloadsAmounts)
			->with('roughItems', $roughItems)
            ->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;
    }

    protected function customGridTab2()
    {
		$headers = ["日期", "當日總下載數", "詳細下載影片資訊"];

		$items = ReportService::getDownloadsItem($this->start_date, $this->end_date)->get();
		
		if($items)
		{
			$roughItems = $items
				->groupBy(function ($val) {
					return \Carbon\Carbon::parse($val->updated_at)->format('y/m/d');
				})
				->map(function ($value, $key) {
					$value['sumUsers'] = $value->unique('id')->count();
					return $value;
				})
				->toArray();

			$downloadTotal = $items->count();
			//dd($downloadTotal);

			$detailItems = $items
				->groupBy(function ($val) {
					return \Carbon\Carbon::parse($val->updated_at)->format('y/m/d');
				})
				->map(function ($value, $key) {
					$value->groupBy(function ($val) {
						return \Carbon\Carbon::parse($val->updated_at)->format('y/m/d');
					});

					return $value;
				})
				->toArray();

			$tmpMemos = [];
			$tmpTotalMemo = [];
			foreach($detailItems as $key1 => $detailItem)
			{
				foreach($detailItem as $key2 => $item)
				{
					$tmpMemo[] = $item["memo"];
					$tmpTotalMemo[] = $item["memo"];
				}
				$detailItems[$key1][$key1] = array_count_values($tmpMemo);
				unset($tmpMemo);
			}

			$totalMemo = array_count_values($tmpTotalMemo);
		}

		$view = view('admin.report.cellsDownloadService')
            ->with('headers', $headers)
			->with('roughItems', $roughItems)		//每日總下載數
			->with('detailItems', $detailItems)		//每日下載細節
			->with('downloadTotal', $downloadTotal)		//下載總和
			->with('totalMemo', $totalMemo)		//下載總和細節

            ->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;
    }

	/*
    protected function customGridTab3( Request $request )
    {
		$request->key = '20'.$request->key;
		$items = ReportService::getDownloadsItem($request->key, $request->key)->get()->toArray();
		dd($items);
	}
	*/

    protected function customGrid()
    {
		$headers = ["總次數 ( 不分帳號 )", "人數 ( 帳號數 )"];

		$items = ReportService::getCoinsItem($this->start_date, $this->end_date);
		if($items)
		{
			$totalSumByAllAccounts = $items->selectRaw('count(user_id) as totalAccount, user_id')
							->get()->toArray()[0]['totalAccount'];

			$sumByItems = $items->selectRaw('count(user_id) as totalAccount, user_id')
							->groupBy('coins')
							->get()
							->toArray();
		}



		$items = ReportService::getCoinsItem($this->start_date, $this->end_date);
		if($items)
		{
			$contentItems = $items->get()->groupBy('coins');

			$contentItems =  $contentItems->map(function ($item) {
				return $item->unique('user_id')->count();
			});
			$contentItems =  $contentItems->toArray();

			$totalSumByAllUsers = array_sum($contentItems);
		}

		$keyCount = 0;

		foreach($contentItems as $contentItem)
		{
			$sumByItems[$keyCount]['totalUsers'] = $contentItem;
			$keyCount++;
		}


		$items = ReportService::getDownloadsItem($this->start_date, $this->end_date)->get();

		if($items)
		{
			//下載總次數
			$totalDownloadsByAllUsers = $items->count();

			//下載總次數 by 帳號
			$totalDownloadsByAllAccounts =  $items->unique('user_id')->count();

			$totalDownloadsAmounts = abs($items->sum('coins'));
			//dd( $totalDownloadsAmounts );
		}

		$view = view('admin.report.cellsService')
            ->with('headers', $headers)
			->with('sumByItems', $sumByItems)
			->with('totalSumByAllAccounts', $totalSumByAllAccounts)
			->with('totalSumByAllUsers', $totalSumByAllUsers)
			->with('totalDownloadsByAllUsers', $totalDownloadsByAllUsers)
			->with('totalDownloadsByAllAccounts', $totalDownloadsByAllAccounts)
			->with('totalDownloadsAmounts', $totalDownloadsAmounts)
            ->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;
    }

}
