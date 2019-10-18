<?php

namespace App\Admin\Controllers;

use App\AvTag;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AvTagController extends Controller
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
        return Admin::grid(AvTag::class, function (Grid $grid) {
			$grid->filter(function($filter){

				// 去掉默认的id过滤器
				//$filter->disableIdFilter();

				// 在这里添加字段过滤器
				$filter->like('name', 'Tag');
				$filter->like('name_cn', 'Tag简体');
			});

			$grid->model()->orderBy('updated_at', 'desc');
            $grid->id('ID')->sortable();
            $grid->name('原始名稱')->label('success');;
            $grid->name_cn('簡體名稱')->label('danger');
            $grid->views('點擊數');

            $grid->created_at('更新日期');
            $grid->updated_at('建立日期');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(AvTag::class, function (Form $form) {

            $form->display('id', 'ID');
			$form->text('name', '原始名稱')->rules('required|min:2', [
				'required' => '不能空白',
				'min'   => '不能少于2个字符',
			]);
			$form->text('name_cn', '簡體名稱');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
