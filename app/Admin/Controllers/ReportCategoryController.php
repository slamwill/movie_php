<?php

namespace App\Admin\Controllers;

use App\ReportCategoryMonth;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Tab;
use Illuminate\Http\Request;

class ReportCategoryController extends Controller
{
    use ModelForm;

    private $days = 7;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {

            // 確認搜尋區間
            $start_date = $request->input('start_date');
			$end_date = $request->input('end_date');
			if (!$start_date) $start_date = date('Y/m/d',strtotime('-7 days'));	
            if (!$end_date) $end_date = date('Y/m/d',strtotime('-1 days'));	
            // if (!$end_date) $end_date = date('Y/m/d');
            $this->days = $this->countDays($start_date, $end_date);

            $content->header('每日點擊最高分類');
            $content->description('每日點擊最高分類');

            // 搜尋用的表單
            $content->row(view('admin.report.daterange')
                    // ->with('action', '/admin/api/ReportCategory')
                    // ->with('range', $set_range)
                    ->with('start_date', $start_date)
                    ->with('end_date', $end_date)
                    ->render());

            // 報表顯示
            $tab = new Tab();
            $tab->add('未登入', $this->customGrid(0));
            $tab->add('已登入', $this->customGrid(1));
            $content->row($tab->render());

        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('每日點擊最高分類');
            $content->description('每日點擊最高分類');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('每日點擊最高分類');
            $content->description('每日點擊最高分類');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ReportCategoryMonth::class, function (Grid $grid) {
            $grid->column('sequence');
        });
    }

    protected function customGrid($login = 0)
    {
        $days = $this->days;
        $headers = [''];
        $rows = [];

        // 取得header
        for($i=0; $i<$days; $i++){
            $date = date('Y-m-d', strtotime(sprintf('%d days ago +%d day', $days, $i)));
            if($i == 0){
                array_push($headers, $date);
            }else{
                array_push($headers, $date);
            }
        }

        // 取得cell數據
        for($j=0; $j<10; $j++){
            $row = ['seq' => $j + 1];
            for($i=0; $i<$days; $i++){
                $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $days, $i)));
                $raw = ReportCategoryMonth::where('login', $login)
                    ->where('date', $date)
                    ->where('sequence', $j+1)
                    ->get()
                    ->toArray();
                if(count($raw)){
                    $row['cell' . $i] = [$raw[0]['title'], $raw[0]['views'], env('APP_URL') . '/' . $raw[0]['record']];
                }else{
                    $row['cell' . $i] = null;
                }
            }
            $rows[$j] = $row;
        }

        return view('admin.report.cells')
            ->with('headers', $headers)
            ->with('rows', $rows)
            ->render();
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ReportCategoryMonth::class, function (Form $form) {
//            $form->display('id', 'ID');
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
        });
    }


    public function makeCSV($results = [], $name)
    {
        header('Content-Type: application/csv; charset=utf-8;');
        header('Content-Disposition: attachment; filename='. $name . '.csv');
        header('Pragma: no-cache');
        header("Expires: 0");

        $outstream = fopen("php://output", "w");
        fputs($outstream, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($outstream, array_keys($results[0]));

        foreach($results as $result)
        {
            fputcsv($outstream, $result);
        }

        fclose($outstream);
        die;
    }

    public function countDays($date1, $date2)
    {
        $datetime1 = strtotime(trim($date1));
        $datetime2 = strtotime(trim($date2));

        $diff = $datetime1 - $datetime2;

        if($diff > 0) {
            return 7;
        }else {
            return abs($diff) / 86400 + 1;
            // return abs($diff) / 86400;
        }
    }
}
