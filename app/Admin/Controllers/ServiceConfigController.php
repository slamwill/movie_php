<?php

namespace App\Admin\Controllers;

use App\ServiceConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ServiceConfigController extends Controller
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

            $content->header('header');
            $content->description('description');

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

            $content->header('方案設定');
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
        return Admin::grid(ServiceConfig::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->title('名稱');
            $grid->coins('點數/扣點');
            $grid->times('加值/分鐘');
			$enable = [
				'on'  => ['value' => 'on', 'text' => '開啟', 'color' => 'primary'],
				'off' => ['value' => 'off', 'text' => '關閉', 'color' => 'default'],
			];
			$grid->enable('開關')->switch($enable);
            $grid->created_at('建立時間');
            $grid->updated_at('異動時間');
			$grid->filter(function ($filter) {

				// 设置created_at字段的范围查询
				//$filter->between('created_at', 'Created Time')->datetime();
				$filter->like('title', '名稱');
			});
		
		});
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ServiceConfig::class, function (Form $form) {

            $form->display('id', 'ID');
			$form->text('title', '名稱')->rules('required');
			$form->number('coins', '點數/扣點')->rules('required');
			$form->number('times', '加值/分鐘')->rules('required');
			$form->radio('enable' , '開關')->options(['on' => '開啟', 'off'=> '關閉'])->default('on');

			$form->display('created_at', '建立時間');
            $form->display('updated_at', '異動時間');
        });
    }
}
