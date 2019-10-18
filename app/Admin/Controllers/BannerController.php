<?php

namespace App\Admin\Controllers;

use App\Banner;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Redis;

class BannerController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Banner管理');
            $content->description('維護與管理首頁用的Banner');

            $content->body($this->grid());
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

            $content->header('Banner管理');
            $content->description('維護與管理首頁用的Banner');

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

            $content->header('Banner管理');
            $content->description('維護與管理首頁用的Banner');

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
        return Admin::grid(Banner::class, function (Grid $grid) {
            $grid->model()->orderBy('ID','desc');
            $grid->id('ID')->sortable();
            $grid->start_time('露出日期');
            $grid->end_time('結束日期');
            $grid->image_1('電腦版圖片')->display(function ($image){
                $image = asset('uploads/'.$image);
                return '<a class="fancybox" href="'.$image.'" data-fancybox="images"><img src="'.$image.'" style="max-width:100px;"></a>';
            });
            $grid->image_2('手機版圖片')->display(function ($image){
                $image = asset('uploads/'.$image);
                return '<a class="fancybox" href="'.$image.'" data-fancybox="images"><img src="'.$image.'" style="max-width:100px;"></a>';
            });
            $grid->url('連結網址');
            $grid->view_1('點擊數(未登入)');
            $grid->view_2('點擊數(已登入)');
			$enable = [
				'on'  => ['value' => '1', 'text' => '啟用', 'color' => 'primary'],
				'off' => ['value' => '0', 'text' => '關閉', 'color' => 'default'],
			];
			$grid->enable('啟用')->switch($enable);
            $grid->created_at('建立日期');
            $grid->updated_at('更新日期');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Banner::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->datetimeRange('start_time', 'end_time', '顯示期間')
                 ->rules('required', [
                    'required' => '請設定顯示期間'
                 ]);
            $form->file('image_1', '電腦版圖片')
                 ->rules('required', [
                     'required' => '請選擇一張圖片'
                 ]);
            $form->file('image_2', '手機版圖片')
                 ->rules('required', [
                    'required' => '請選擇一張圖片'
                 ]);
            $form->text('url', '連結網址')
                ->rules('required', [
                    'required' => '請輸入連結網址'
                ]);
            $form->switch('enable', '啟用');
            $form->display('created_at', '建立時間');
            $form->display('updated_at', '更新時間');
        });
    }
}
