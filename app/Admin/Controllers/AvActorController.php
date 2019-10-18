<?php

namespace App\Admin\Controllers;

use App\AvActor;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Intervention\Image\ImageManagerStatic as Image;

class AvActorController extends Controller
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
			
			$script = 
<<<EOF
	$(function (){
		$(".fancybox").fancybox();


	});
EOF;
			
			Admin::script($script);


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

            $content->body($this->form(true)->edit($id));
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
        return Admin::grid(AvActor::class, function (Grid $grid) {

			
			
			$grid->model()->orderBy('updated_at', 'desc');

            $grid->id('ID')->sortable();

//			$grid->column('image','縮圖')->display(function ($url){
//				return $url;
//			});

			$grid->column('image','頭像')->display(function ($image){
				$src = $this->image ? config('actor_url').$this->image : config('actor_url').'no-image.jpg';
				return '<a class="fancybox" href="'.$src.'" data-fancybox="images"><img src="'.$src.'" width="100" class="img-thumbnail"></a>';
			});			
			/*
			$grid->column('image','圖片')->image(null,200,200)->display(function ($image){
				$src = config('filesystems.disks.admin.url').'/'.$this->image;
				return '<a class="fancybox" href="'.$src.'">'.$image.'</a>';
			});*/
			$grid->column('name','女優名字')->label('success');
			$grid->column('info','女優資訊')->display(function (){
			
				return '
					<span class="label label-info">譯名：'.$this->nickname.'</span>
					<span class="label label-primary">生日：'.$this->birthday.'</span>
					<span class="label label-default">身高：'.$this->height.'</span>
					<span class="label label-danger">罩杯：'.$this->cup.'</span>
				
				';
			});
			//$grid->column('spider','爬蟲')->display(function (){
			//	return '<div class="btn-group"><a class="btn btn-default "><i class="fa fa-refresh"></i>&nbsp;重新抓取</a></div>';
			//});

			$grid->updated_at('更新日期');
            $grid->created_at('建立日期');

			$grid->actions(function ($actions) {
				$actions->disableDelete();
				//$actions->disableEdit();
			});
		
		});
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEdit = false)
    {
        return Admin::form(AvActor::class, function (Form $form) use ($isEdit) {


            $form->display('id', 'ID');
			
			$form->text('name', '女優名稱')->rules('required', [
				'required'   => '不可空值',
			]);
			$form->text('nickname', '女優譯名');
			$form->date('birthday', '女優生日');
			$form->number('height', '女優身高');

			$form->select('cup','罩杯')->options(['A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K',]);
			if ($isEdit) {
			
			$form->text('origin_image','來源圖片');
			$form->html(function (){
				return '<div class="btn-group"><a class="btn btn-warning refetch-btn""><i class="fa fa-retweet"></i>&nbsp;重新抓取來源圖片</a></div>';
			});
            $form->display('image', '女優圖像')->with(function ($url){

				$script ="
				$(function (){

					$('.refetch-btn').click(function (){
						var origin_image = $('#origin_image').val();
						console.log(origin_image);
						$('body').mLoading({text:'圖片重抓中，請稍候....'});
						$.ajax({
							method: 'post',
							url: '".route('admin.api.fetchActorImage')."',
							data: { 
								'id': {$this->id},
								'_token': LA.token,
								url:origin_image,
							} ,
							success: function (response) {
								if (response.status) {
									$('#av-image').attr('src', response.actor_url + '?' + Math.random());
									toastr.success('抓取成功');

								}
								else {
									toastr.success('抓取失敗');
								}
								$('body').mLoading('hide');
							}
						});
					});
				});
				";
				Admin::script($script);		
				
				
				return '<img src="'.config('actor_url').'/'.($url ? $url : 'no-image.jpg').'" class="img-thumbnail" id="av-image">';
			});

			}
			//檔名隨機，
			//$form->image('image','圖片上傳')->uniqueName();
			//var_dump( $form->model()->avkey);
//			$form->image('image','圖片上傳')->move(public_path('uploads/actors/'), md5($videos['avkey']).'.jpg');

			// 修改上传目录
			//$form->image('picture')->move('public/upload/image1/');
			$form->divide();

			$form->display('created_at', '建立時間');
            $form->display('updated_at', '更新時間');

			$form->saving(function (Form $form) {

			});		
		
		});
    }
	//抓圖
	public function fetchActorImage(Request $request){

		try {

			$actor_path = public_path('uploads/actors/' . md5($request->id).'.jpg');
			Image::make($request->url)->save($actor_path);

		} catch (\Intervention\Image\Exception\NotReadableException $e) {
			return response()->json(['status' => false]);
		}

		return response()->json(['status' => true , 'actor_url' => config('actor_url').'/'. md5($request->id).'.jpg']);
			//return response::json(['a':2]);
	}

	//delete
    public function destroy($id)
    {
		$AvActor = \App\AvActor::where('id',$id)->first();
		if ($AvActor->image) {
			@unlink( public_path('uploads/actors/' . $AvActor->image) );
		}
        if ($this->form()->destroy($id)) {
			\App\AvVideoActor::where('actor_id', $id )->delete();
			return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

}
