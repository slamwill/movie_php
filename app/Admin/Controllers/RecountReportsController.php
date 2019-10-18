<?php

namespace App\Admin\Controllers;

use App\ReportMonth;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RecountReportsController extends Controller
{
    use ModelForm;

    private $days = 1;
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
            if (!$this->start_date) $this->start_date = date('Y/m/d',strtotime('-1 days'));
            if (!$this->end_date) $this->end_date = date('Y/m/d',strtotime('-1 days'));
            // if (!$end_date) $end_date = date('Y/m/d');
            $this->days = $this->countDays($this->start_date, $this->end_date);

            $content->header('重算統計數據');
            $content->description('重算統計數據');

            //接收資料
            $report_type = $request->input('report_type');

            if(!empty($report_type) && $this->executeRecount($report_type)){
                //$content->row('重算完成');
                $content->row(view('admin.report.recount')
                    ->with('start_date', $this->start_date)
                    ->with('end_date', $this->end_date)
                    ->with('message', '重算完成')
                    ->with('report_type', $report_type)
                    ->render());
            }else {
                // 搜尋用的表單
                $content->row(view('admin.report.recount')
                    ->with('start_date', $this->start_date)
                    ->with('end_date', $this->end_date)
                    // ->with('message', '請選擇重算報表類別')
                    ->render());
            }
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

            $content->header('header');
            $content->description('description');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(ReportMonth::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ReportMonth::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
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

    private function executeRecount($report_type)
    {
        $result = false;

        do{
            $RecountPageview = new \App\Console\Jobs\CountPageview();
            switch($report_type){
                case 'ReportCategoryMonth':
                    for($i=0; $i<$this->days; $i++){
                        $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $this->days, $i)));
                        $RecountPageview->setYmd($date);
                        $RecountPageview->recountPageViewCategory();
                    }
                    $result = true;
                    break;

                case 'ReportWatchMonth':
                    for($i=0; $i<$this->days; $i++){
                        $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $this->days, $i)));
                        $RecountPageview->setYmd($date);
                        $RecountPageview->recountPageviewWatch();
                    }
                    $result = true;
                    break;

                case 'ReportTagMonth':
                    for($i=0; $i<$this->days; $i++){
                        $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $this->days, $i)));
                        $RecountPageview->setYmd($date);
                        $RecountPageview->recountPageviewTag();
                    }
                    $result = true;
                    break;

                case 'ReportMonth':
                    for($i=0; $i<$this->days; $i++){
                        $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $this->days, $i)));
                        $RecountPageview->setYmd($date);
                        $RecountPageview->recountPageviewDaily();
                    }
                    $result = true;
                    break;

                default:
                    break;
            }
        }while(0);

        return $result;
    }
}
