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
use Encore\Admin\Widgets\Box;

class ReportMonthController extends Controller
{
    use ModelForm;

    private $days = 7;
    private $start_date;
    private $end_date;
    private $group = null;
    private $keyword = null;

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

            if($request->has('group')) $this->group = $request->input('group');
            if($request->has('keyword')) $this->keyword = $request->input('keyword');

            if (!$this->start_date) $this->start_date = date('Y/m/d',strtotime('-7 days'));
            if (!$this->end_date) $this->end_date = date('Y/m/d',strtotime('-1 days'));
            $this->days = $this->countDays($this->start_date, $this->end_date);

            $content->header('月報表');
            $content->description('月報表');

            // 搜尋用的表單
            $content->row(view('admin.report.month.daterange')
                ->with('start_date', $this->start_date)
                ->with('end_date', $this->end_date)
                ->with('group', $this->group)
                ->with('keyword', $this->keyword)
                ->render());

            $content->row($this->customGrid());
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

            $content->header('月報表');
            $content->description('月報表');

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

            $content->header('月報表');
            $content->description('月報表');

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

    protected function customGrid()
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

        // 取得cell的項目名稱
        $items = ReportMonth::getItemNames($this->start_date, $this->end_date, $this->group, $this->keyword)->get()->toArray();

        // 取得cell數據
        for($j=0; $j<count($items); $j++){
            $item_name = $items[$j]['title'];
            $row = ['seq' => $item_name];
            for($i=0; $i<$days; $i++){
                $date = date('Ymd', strtotime(sprintf('%d days ago +%d day', $days, $i)));
                $raw = ReportMonth::where('date', $date)
                    ->where('title', $item_name)
                    ->get()
                    ->toArray();
                if(count($raw)){
                    $row['cell' . $i] = $raw[0]['views'];
                }else{
                    $row['cell' . $i] = 0;
                }
            }
            $rows[$j] = $row;
        }

        $view = view('admin.report.cells')
            ->with('headers', $headers)
            ->with('rows', $rows)
            ->render();

        $box = new Box();
        $box->title('列表');
        $box->content($view);
        return $box;
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
