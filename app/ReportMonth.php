<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportMonth extends Model
{
    protected $table = 'report_month';
    public $timestamps = false;

    public static function getItemNames($start_date, $end_date, $group = null, $keyword = null)
    {
        $start_date = date('Ymd', strtotime($start_date));
        $end_date = date('Ymd', strtotime($end_date));

        //select title from report_month where `date` between '20180714' and '20180717' group by `title` order by `sequence`;
        $query = self::whereBetween('date', [$start_date, $end_date]);
        if(!is_null($keyword)) $query = $query->where('title', 'like', '%' . $keyword . '%');
        if(!is_null($group)){
            switch($group){
                case 'basic':
                    $query = $query->whereIn('sequence', [1,2,3,4,5]);
                    break;
                case 'banner':
                    $query = $query->where('sequence', 6);
                    break;
                case 'category':
                    $query = $query->where('sequence', 7);
                    break;
                case 'tag':
                    $query = $query->where('sequence', 8);
                    break;
            }
        }

        return $query->select('title')
            ->groupBy('title')
            ->orderBy('sequence', 'asc');
    }
}
