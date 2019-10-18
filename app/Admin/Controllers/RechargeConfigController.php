<?php

namespace App\Admin\Controllers;

use App\RechargeConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RechargeConfigController extends Controller
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

            $content->header('充值金額設定');
            $content->description('');

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

            $content->header('充值金額設定');
            $content->description('');

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

            $content->header('充值金額設定');
            $content->description('');

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
        return Admin::grid(RechargeConfig::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->title('名稱');
            $grid->amount('金額');
            $grid->coins('加值點數');
            $grid->currency('幣別')->display(function ($text){
				$array = config('av.currency');
				return $array[$text];
			});
            $grid->description('描述');

			$enable = [
				'on'  => ['value' => 'on', 'text' => '開啟', 'color' => 'primary'],
				'off' => ['value' => 'off', 'text' => '關閉', 'color' => 'default'],
			];
			$grid->enable('開關')->switch($enable);

            $grid->service_id('對應服務')->display(function ($id){
				if (!$id) return '無';
				$ServiceConfig = \App\ServiceConfig::find($id);
				return $ServiceConfig->title.'( -'.$ServiceConfig->coins.' 點 / + '.$ServiceConfig->days.' 天)';
			});

            $grid->created_at('建立時間');
            $grid->updated_at('異動時間');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(RechargeConfig::class, function (Form $form) {

            $form->display('id', 'ID');

			//$form->text('title', '名稱')->rules('required|min:4', [
			//	'min'   => '不能少于2个字符',
			//]);
			$form->text('title', '名稱')->rules('required');
			$form->number('amount', '金额')->rules('required');
			$form->number('coins', '加值點數')->rules('required');
			
			$form->radio('currency' , '幣別')->options(config('av.currency'))->default('NT');
			/*
			$form->number('amount', '金额')->rules('required|min:4', [
				'min'   => '不能少于4个字符',
			]);

			$form->number('days', '加值時間')->rules('required|min:4', [
				'min'   => '不能少于4个字符',
			]);*/
			//$form->text('account', '帳號');
            //$form->text('password', '密碼');
            $form->text('description', '描述')->rules('required');
			//$form->select('service_id','對應服務ID')->options([0 => '無碼', 1 => '有碼'])->default('0');
			$form->select('service_id','對應服務')->options(\App\ServiceConfig::where('enable','on')->pluck('title','id'));

			$form->radio('enable' , '開關')->options(['on' => '開啟', 'off'=> '關閉'])->default('on');

            $form->display('created_at', '建立時間');
            $form->display('updated_at', '異動時間');
        });
    }
}
