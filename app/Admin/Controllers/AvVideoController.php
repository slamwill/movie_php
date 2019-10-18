<?php

namespace App\Admin\Controllers;

use App\AvVideo;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

use Intervention\Image\ImageManagerStatic as Image;


class AvVideoController extends Controller
{
    use ModelForm;

	public function __construct(){
			Admin::script($this->script());
	
	}
	public function spider(Request $request){

		if($request->target == 'javbus') {		
			\Artisan::call('spider:video', ['avkey' => [$request->avkey, $request->url]]);
		}
		if($request->target == 'javbook') {		
			$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
			\Artisan::call('spider:javbook', ['avkey' => [$request->avkey, $AvVideo->video_source, $request->url]]);
		}

		if($request->target == 'jav101') {		
			$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
			\Artisan::call('spider:jav', ['avkey' => [$request->avkey, $AvVideo->video_source]]);
		}

		/*
		if (strtolower(substr($request->avkey,0,4)) == 'avid') {
			$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
			\Artisan::call('spider:jav', ['avkey' => [$request->avkey, $AvVideo->video_source]]);
		}
		else {
			\Artisan::call('spider:video', ['avkey' => [$request->avkey, $request->url]]);
		}*/
	}

	protected function script() {

		return <<<EOF

$(function (){

	$('.spider-btn').click(function (){
		var avkey = $(this).data('avkey');
		var url = $(this).data('url');
		var target = $(this).data('target');
		//console.log(avkey);
		$('body').mLoading({text:'抓取中，請稍候....'});
		$.ajax({
			method: 'post',
			url: 'api/spider',
			data: { 'avkey': avkey,'url':url,'target':target,'_token': LA.token},
			success: function () {
				$('body').mLoading("hide");
				$('.grid-refresh').click();
				$.pjax.reload('#pjax-container');
				toastr.success('刷新完成 !');
				//toastr.success('執行完成');
			},
			error:function (){
				$('body').mLoading("hide");
				//$('.grid-refresh').click();			
				toastr.error('抓取失敗');
			}
		});

	});


	$("[data-fancybox]").fancybox({
		iframe : {
			css : {
				width : '640px',
				height : '360px',
			}
		}
	});
});

EOF;
	}

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

            $content->body($this->form(true)->edit($id));
        });
    }

	public function update($id)
	{
		return $this->form($id)->update($id);
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
        return Admin::grid(AvVideo::class, function (Grid $grid) {

			$grid->filter(function($filter){

				// 去掉默认的id过滤器
				//$filter->disableIdFilter();

				// 在这里添加字段过滤器
				$filter->like('avkey', '番號');
			});

			//$grid->tools(function ($tools) {
			//	$tools->batch(function ($batch) {
			//		$batch->disableDelete();
			//	});
			//});
			//$grid->paginate(1); //分頁
			$grid->tools(function ($tools) {
				$tools->append(new \App\Admin\Tools\AvVideo);
			});

			if (in_array(\Request::get('video_source'), [1, 2, 3, 4])) {
				$grid->model()->where('video_source', \Request::get('video_source') - 1)->orderBy('updated_at','desc');
			}
			if (in_array(\Request::get('video_type'), [1, 2])) {
				$grid->model()->where('video_type', \Request::get('video_type') - 1)->orderBy('updated_at','desc');
			}
			if (in_array(\Request::get('has_video'), [1, 2])) {
				$grid->model()->where('has_video', \Request::get('has_video') - 1)->orderBy('updated_at','desc');
			}
			if (in_array(\Request::get('is_free'), [1, 2])) {
				$grid->model()->where('is_free', \Request::get('is_free') - 1)->orderBy('updated_at','desc');
			}
			if (in_array(\Request::get('enable'), ['on','off'])) {
				$grid->model()->where('enable', \Request::get('enable'))->orderBy('updated_at','desc');
			}

			//$grid->model()->orderBy('updated_at', 'desc');
			$grid->model()->orderBy('id', 'desc')->orderBy('updated_at', 'desc');
			//$grid->model()->orderBy('release_date', 'desc')->orderBy('id', 'desc');

			$grid->id('ID')->sortable();


			$grid->column('origin_cover','封面')->display(function ($image){
				//if (!$this->is_free) {
					$image = config('cover_url').$this->cover;
					if ($this->cover_index) {
						$image = \App\Classes\Common::previewToken($this->avkey.'/preview'.($this->cover_index-1).'s.png');
					
					}
				//}
				//return '<a class="fancybox" href="'.$src.'" data-fancybox="images"><img src="'.$src.'?'.time().'" width="170"></a>';
				return '<a class="fancybox" href="'.$image.'" data-fancybox="images"><img src="'.$image.'" width="170"></a>';
			});
			/*
			$grid->column('thumbnail','縮圖')->display(function ($image){
				$src = config('thumbnail_url').$this->thumbnail;
				//return '<a class="fancybox" href="'.$src.'" data-fancybox="images"><img src="'.$src.'?'.time().'" width="80"></a>';
				return '<a class="fancybox" href="'.$src.'" data-fancybox="images"><img src="'.$src.'" width="80"></a>';
			});*/



			$grid->title('標題')->display(function ($title){
				$video_type = $this->video_type ? '<span class="label label-info">有碼</span>' : '<span class="label label-danger">無碼</span>';			
				$html  = '<p class="width-8"> '.$video_type.' <span class="label label-warning">'.$this->avkey.'</span> </p>';
				$html .= '<p>'.$title.'</p>';


				$actorString = '女優：';
				if ($this->actors) {
					$actors = \App\AvActor::find(explode(',',$this->actors))->pluck('name')->toArray();
					foreach ($actors as $actor){
						$actorString .= '<span class="label label-success">'.$actor.'</span> ';
					}
				}

				
				$tagString = '標籤：';
				if ($this->tags) {
					$tags = \App\AvTag::find(explode(',',$this->tags))->pluck('name')->toArray();
					foreach ($tags as $tag){
						$tagString .= '<span class="label label-default">'.$tag.'</span> ';
					}
				}

				$has_video = $this->has_video ? '<a data-fancybox data-type="iframe" data-src="'.route('admin.api.video',['id'=>$this->id]).'" href="javascript:void(0);" data-width="400" data-height="200">
								<span class="label label-danger "><i class="fa fa-video-camera"></i> 影片播放</span>
							</a>':'';


				$html .= '<p>'.$title.'</p>';
				return '
					<div class="videos-list">
						<p class="width">  
							
							'.$has_video.'
							'.$video_type.'
							<span class="label label-warning">'.$this->avkey.'</span>
							<span class="label label-default">發佈日期:'.$this->release_date.'</span>
						</p>
						<p>'.$title.'</p>
						  
						<p class="width nowrap">'.$actorString.'</p>
						<p class="width nowrap">'.$tagString.'</p>
					</div>
				
				';
			});


			/*
			$grid->column('actors','女優')->display(function ($actor){
				$actors = explode(',', $actor);
				$AvActors = \App\AvActor::find($actors)->pluck('name')->toArray();
				$html = '';
				foreach ($AvActors as $actor){
					$html .= '<span class="label label-success">'.$actor.'</span> ';
				}
				return $html;
			});	*/	
			$grid->video_source('影片種類')->radio(config('av.video_source'));
			/*
			$grid->video_type('兵種')->display(function ($val){
				return $val ? '<span class="label label-primary">有码</span>' :'<span class="label label-default">无码</span>'  ;
			});*/
			
			/*
			$grid->video_type('兵種')->radio([
				'0' => '步兵(無馬)',
				'1' => '騎兵(有馬)',
			]);*/			
			/*
			$grid->enable('上架')->radio([
				'on' => '上架',
				'off' => '下架',
			]);*/
			
			$enable = [
				'on'  => ['value' => 'on', 'text' => '上架', 'color' => 'primary'],
				'off' => ['value' => 'off', 'text' => '下架', 'color' => 'default'],
			];
			$grid->enable('上架')->switch($enable);

			$grid->auto_release_date('自動上架時間');


			//$enable = [
			//	'on'  => ['value' => '0', 'text' => '付費', 'color' => 'primary'],
			//	'off' => ['value' => '1', 'text' => '免費', 'color' => 'default'],
			//];
			
			//$grid->is_free('付費')->switch($enable);
			//$grid->is_free('付費')->display(function ($val){
			//	return $val ? '<span class="label label-default">免費</span>' :'<span class="label label-info">付費</span>'  ;
			
			//});

			$grid->column('spider','爬蟲')->display(function (){
				return '<div class="btn-group">
					<a class="btn btn-default spider-btn btn-sm" data-avkey="'.$this->avkey.'" data-url="'.$this->spider_url.'" data-target="javbus"><i class="fa fa-refresh"></i>&nbsp;JavBus</a><br>
					<a class="btn btn-default spider-btn btn-sm" data-avkey="'.$this->avkey.'" data-url="'.$this->spider_url.'" data-target="javbook"><i class="fa fa-refresh"></i>&nbsp;JavBook</a><br>
					<a class="btn btn-default spider-btn btn-sm" data-avkey="'.$this->avkey.'" data-url="'.$this->spider_url.'" data-target="jav101"><i class="fa fa-refresh"></i>&nbsp;啪啪研習所</a>
				</div>';
			});
			//$grid->created_at('建立時間');
            $grid->updated_at('更新時間');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEdit = false)
    {


        return Admin::form(AvVideo::class, function (Form $form) use ($isEdit) {


			$form->tab('基本資訊', function ($form) use ($isEdit){

				
				if ($isEdit) {
					$form->display('id', 'ID');
					$form->display('avkey', '番號(AVKEY)');
				
					/*
					$form->text('avkey', '番號(AVKEY)')->rules('required|min:2', [
						'required' => '不能空白',
						'min'   => '不能少于2个字符',
					])->attribute(['readonly' => 'readonly']);*/
				}
				else {
					$form->text('avkey', '番號(AVKEY)')->rules('unique:av_videos,avkey|required|min:2', [
						'unique' => 'Avkey 已存在',
						'required' => '不能空白',
						'min'   => '不能少于2个字符',
					]);			
				}
				$form->text('spider_url', '爬蟲網址')->placeholder('例如 https://avso.club/ja/search/113017_180');
				//$form->hidden('origin_cover', '來源圖片');
				//$form->hidden('cover_index', '封面代號');

				/*
				$form->text('title', '標題')->rules('required|min:2', [
					'required' => '不能空白',
					'min'   => '不能少于2个字符',
				]);*/
				$form->text('title', '標題');

				$form->textarea('content','簡述');

				$form->text('m3u8_url', 'M3U8連結');
				$form->text('mp4_url', 'MP4連結');


				$form->select('video_source','來源')->options(config('av.video_source'))->default('0');
				$form->select('video_type','影片')->options(config('av.video_type'))->default('0');

				$form->multipleSelect('actors', '演出女優')->options(function ($ids){
					return $ids ? \App\AvActor::find($ids)->pluck('name', 'id') : false;
				})->ajax('/'.config('admin.route.prefix').'/api/actors');

				//$form->multipleSelect('tags', '標籤')->options(function ($ids){
				//	return \App\AvTag::find($ids)->pluck('name', 'id');
				//})->ajax('/'.config('admin.route.prefix').'/api/tags');

				$form->multipleSelect('tags', '標籤')->options(\App\AvTag::all()->pluck('name','id'));


				$form->time('duration','影片時間')->format('HH:mm:ss');
				$form->date('release_date','發佈日期')->format('YYYY-MM-DD');

				$states = [
					'on'  => ['value' => 'on', 'text' => '上架', 'color' => 'success'],
					'off' => ['value' => 'off', 'text' => '下架', 'color' => 'default'],
				];
				$form->switch('enable', '是否上架')->states($states)->default('off');

				$form->datetime('auto_release_date','自動上架時間')->format('YYYY-MM-DD HH:mm:ss');

				$states = [
					'on'  => ['value' => '0', 'text' => '付費', 'color' => 'primary'],
					'off' => ['value' => '1', 'text' => '免費', 'color' => 'default'],
				];
				$form->switch('is_free', '影片分類')->states($states)->default('on');

				$states = [
					'off'  => ['value' => '0', 'text' => '無', 'color' => 'default'],
					'on' => ['value' => '1', 'text' => '有', 'color' => 'primary'],

				];
				$form->switch('has_video', '有無影片')->states($states)->default('off');
				if ($isEdit) {
					$form->divide();
					$form->display('created_at', '建立時間');
					$form->display('updated_at', '更新時間');
				}

			});
				
			if ($isEdit) {
				$form->tab('裁切圖片', function ($form) use ($isEdit){

					//$form->display('origin_cover','來源圖片');
					$form->text('origin_cover', '來源圖片');
					$form->html(function (){
						return '
						<div class="btn-group"><a class="btn btn-warning refetch-btn" data-avkey="'.$this->avkey.'"><i class="fa fa-retweet"></i>&nbsp;重新抓取來源圖片</a></div>
						';
					});
					$form->text('cover','封面');
					$form->html(function (){

						//$image = getimagesize(public_path('/uploads/covers/'.$this->cover));

						$script ="
						var jcropApi,locs={},jcrop;
						$(function (){
							imageSelect = function (c){
								locs = c;
							};

							jcrop = function (){
								if (jcropApi) jcropApi.destroy();
								jcropApi = $('#jcrop-cover').Jcrop({
									a1llowSelect: true,
									aspectRatio: 7.05 / 10,
									onChange: imageSelect,
									setSelect: [0,0,0,0],
								},function (){
									jcropApi = this;
									jcropApi.animateTo([0, 0, 500, 1000]);
									jcropApi.setSelect([0, 0, 500, 1000]);
								});							
							};
							jcrop();

							$('.jcrop-submit').click(function (){
								//console.log(locs);
								var avkey = $(this).data('avkey');
								$.ajax({
									method: 'post',
									url: '".route('admin.api.crop')."',
									data: { 'avkey': avkey,'_token': LA.token,h:locs.h,w:locs.w,x:locs.x,x2:locs.x2,y:locs.y,y2:locs.y2},
									success: function (response) {
										//$('.grid-refresh').click();
										//$.pjax.reload('#pjax-container');
										var thumbnal_url = '".config('thumbnail_url')."';
										$('#thumbnail').attr('src', thumbnal_url + response.thumbnail + '?' + Math.random());
										toastr.success('裁切完成');
									}
								});
							});
							$('.refetch-btn').click(function (){

								var origin_cover = $('#origin_cover').val();
								var avkey = $(this).data('avkey');
								$('body').mLoading({text:'圖片重抓中，請稍候....'});
								$.ajax({
									method: 'post',
									url: '".route('admin.api.fetchCoverImage')."',
									data: { 'avkey': avkey,'_token': LA.token,url:origin_cover},
									success: function (response) {
										if (response.status) {

											$('#jcrop-cover').attr('src', response.cover_url + '?' + Math.random());

											jcrop();
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
						$src = config('cover_url').$this->cover;
						return '
							<img src="'.$src.'" id="jcrop-cover">
							<p>
								<div class="btn-group"><a class="btn btn-danger jcrop-submit" data-avkey="'.$this->avkey.'"><i class="fa fa-cut"></i>&nbsp;確定裁切</a></div>
							</p>
						';
					},'');
					$form->divide();
					$form->display('thumbnail','縮圖');
					$form->html(function (){
						$src = config('thumbnail_url').$this->thumbnail;
						return '<img src="'.$src.'" id="thumbnail" class="img-thumbnail">';
					});

				});
				$params = request()->route()->parameters();
				$AvVideo = \App\AvVideo::find($params['AvVideo']);
				//if (!$AvVideo->is_free) {
					$form->tab('選擇封面圖', function ($form) use ($isEdit){


						$arr = [];
						foreach (range(1,20) as $i){
							$arr[$i] = $i.' 號圖';
						}

						$form->select('cover_index','來源')->options($arr);
						$form->html(function (){
							$string = '';

							foreach (range(1, 20) as $number) {
								$url = \App\Classes\Common::previewToken($this->avkey.'/preview'.($number-1).'s.png');
								$string .= '<div class="col-md-3"><img src="'.$url.'" width="100%"><div class="text-center">'.($number).'</div></div>';
							}
							return $string;
						});
			

					});	
				//}
			
			
			
			}
			/*
			if (!$isEdit) {
				$form->saving(function (Form $form){

					$exists = $form->model()->where('avkey',$form->avkey)->count();
					if ($exists) {
						return back()->with('sss');
					}
				
				});
			}*/

			$form->saved(function (Form $form) {

				
				\App\AvVideoTag::where('video_id', $form->model()->id )->delete();
//print_r( $form->model()->id);
				if ($form->model()->tags) {
					$tags = explode(',', $form->model()->tags);

					foreach ($tags as $tag) {
						$AvVideoTag = new \App\AvVideoTag;
						$AvVideoTag->video_id = $form->model()->id;
						$AvVideoTag->tag_id = $tag;
						$AvVideoTag->save();
					}
				}

				\App\AvVideoActor::where('video_id', $form->model()->id )->delete();
				if ($form->model()->actors) {
					$actors = explode(',', $form->model()->actors);
					foreach ($actors as $actor) {
						$AvVideoActor = new \App\AvVideoActor;
						$AvVideoActor->video_id = $form->model()->id;
						$AvVideoActor->actor_id = $actor;
						$AvVideoActor->save();
					}
				}

			});

			//$form->ignore(['column1', 'column2', 'column3']);

        });
    }

	public function crop(Request $request){
		$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
		$imagePath = public_path('uploads/covers/' . $AvVideo->cover);
//print_r($AvVideo);
//echo $imagePath;

		$image = Image::make($imagePath);
//echo time();
//exit;
		$thumbnail =  md5($AvVideo->id).'.jpg';
		$image->crop($request->w,$request->h,$request->x,$request->y)->save(public_path('uploads/thumbnails/'.$thumbnail));
		$AvVideo->thumbnail = $thumbnail;
		$AvVideo->update();
		$AvVideo->touch();
		return $AvVideo;	
	}
	//抓圖
	public function fetchCoverImage(Request $request){

		try {
			$AvVideo = \App\AvVideo::where('avkey',$request->avkey)->first();
			$AvVideo->cover =  md5($request->avkey).'.jpg';
			$AvVideo->save();

			$cover_path = public_path('uploads/covers/' . md5($request->avkey).'.jpg');
			Image::make($request->url)->save($cover_path);

		} catch (\Intervention\Image\Exception\NotReadableException $e) {
			return response()->json(['status' => false]);
		}

		return response()->json(['status' => true , 'cover_url' => config('cover_url').'/'. md5($request->avkey).'.jpg']);
			//return response::json(['a':2]);
	}


	public function fetchActorImage(Request $request){

		try {
			if ($request->id) {
				$AvVideo = \App\AvActor::where('id',$request->id)->first();
				$AvVideo->image =  md5($request->id).'.jpg';
				$AvVideo->save();
			}

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
		$AvVideo = \App\AvVideo::where('id',$id)->first();
		if ($AvVideo->cover) {
			@unlink( public_path('uploads/covers/' . $AvVideo->cover) );
		}
		if ($AvVideo->thumbnail) {
			@unlink( public_path('uploads/thumbnails/' . $AvVideo->thumbnail) );
		}

        if ($this->form()->destroy($id)) {
			\App\AvVideoTag::where('video_id', $id )->delete();
			\App\AvVideoActor::where('video_id', $id )->delete();
			\App\AvUserVideo::where('video_id', $id )->delete();
			\App\AvUserWatch::where('video_id', $id )->delete();
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



	public function actors(Request $request)
	{
		$q = $request->get('q');
		return \App\AvActor::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
	}

	public function tags(Request $request)
	{
		$q = $request->get('q');
		return \App\AvTag::where('name_cn', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
	}

	public function video(Request $request)
	{
		$AvVideo = \App\AvVideo::where('id',$request->id)->first();
		return view('admin.videoHD', compact('AvVideo'));		
	}

	//flush cache
	public function flushCache(Request $request){
		Redis::select(0);
		Redis::flushdb();
		return 1;
	}

}
