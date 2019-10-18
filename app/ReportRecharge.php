<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportRecharge extends Model
{
	protected $table = 'user_recharges';

    public static function getRMBItem($start_date, $amount)
    {
        $start_date = date('Ymd', strtotime($start_date));
		$query = self::whereBetween('updated_at', [$start_date, date('Ymd', strtotime($start_date . "+1 day")) ])->where('amount', $amount);

        return $query->select('id', 'user_id', 'amount', 'coins')
            ->orderBy('id', 'asc');
    }

    public static function getNewRMBItem($start_date, $end_date)
    {
        //$start_date = date('Ymd', strtotime($start_date));
		//$end_date = date('Ymd', strtotime($end_date . "+1 day"));
        $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
		$end_date = date('Y-m-d 23:59:59', strtotime($end_date));
		$query = self::whereBetween('updated_at', [$start_date, $end_date])->where('status', 1);

		return $query->select('id', 'user_id', 'amount', 'coins', 'currency', 'updated_at')->orderBy('updated_at', 'asc');
		//return $query->select('id', 'user_id', 'amount', 'coins', 'currency', 'updated_at')->orderBy('amount', 'asc');
    }


}
