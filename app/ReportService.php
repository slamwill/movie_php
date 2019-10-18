<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportService extends Model
{
	protected $table = 'transfer_logs';

    public static function getCoinsItem($start_date, $end_date)
    {
        $start_date = date('Ymd', strtotime($start_date));
		$end_date = date('Ymd', strtotime($end_date . "+1 day"));
		$query = self::whereBetween('updated_at', [$start_date, $end_date])->where('type', 2);

        return $query->select('id', 'user_id', 'coins', 'memo')
            ->orderBy('id', 'asc');
    }

    public static function getDownloadsItem($start_date, $end_date)
    {
        $start_date = date('Ymd', strtotime($start_date));
		$end_date = date('Ymd', strtotime($end_date . "+1 day"));
		$query = self::whereBetween('updated_at', [$start_date, $end_date])->where('type', 3);

        return $query->select('id', 'user_id', 'coins', 'memo', 'updated_at')
            ->orderBy('id', 'asc');
    }


}
