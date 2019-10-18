<?php

namespace App\Admin\Controllers;

use App\AdminConfig;
use App\AvTag;

use Encore\Admin\Form;
use Encore\Admin\Form\Field\Html;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AvTagorderController extends Controller
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

            $content->header('標籤順序');
            $content->description('設定前台顯示的標籤順序');

            $box = new Box('順序設定', $this->customForm());
            $box->style('primary');
            $box->solid();
            $content->body($box);

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

    public function storeOrder(Request $request)
    {
        $data['tag_order'] = $request->has('tag_order') ? $request->tag_order : null;

        $json = json_encode($data['tag_order'], JSON_UNESCAPED_UNICODE);
        AdminConfig::where('name', 'tags_order')->update(['value' => $json]);
        return Redirect::to('/admin/AvTagorder');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(AvTag::class, function (Grid $grid) {

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
        return Admin::form(AvTag::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function customForm()
    {
        $tags = config('tags_order');
        $tags = json_decode($tags, true);

        $data = [];
        AvTag::all()->map(function ($item) use (&$data) {
            $data[$item->id] = $item->name;
        });

        $form = new \Encore\Admin\Widgets\Form();
        $form->action(route('admin.api.storeOrder'));
        $form->select('tags', '標籤選項')->options($data);

        $html = <<<HTML
<div class="tag_order" id="sortable" style="width:100%%; padding:1px;">%s</div>
HTML;
        if (!empty($tags)) {
            $html = sprintf($html, $this->tagOrder($tags));
        } else {
            $html = str_replace('%s', '', $html);
            $html = str_replace('%%', '%', $html);
        }

        $form->html($html);

        Admin::script($this->tagOrderScript());

        return $form->render();
    }

    private function tagOrderScript()
    {
        return <<<SCRIPT
$(".tags").change(function(){
    console.log(this.value);

    var thisvalue = this.value;
    var thistext = $(this).find("option:selected").text();
    var html  = '<li class="label label-default" style="margin:4px;">' + thistext + '<span class="select2-selection__clear tag_del label-warning">DEL</span><input type="hidden" name="tag_order[]" value="' + thisvalue + '|' + thistext + '"></li>';

    $(".tag_order").append(html);


    $(".tag_del").click(function(){
        console.log(this);
        $(this).parent().remove();
    });
});

$( "#sortable" ).sortable({
          placeholder: "ui-state-highlight"
});

$(".tag_del").click(function(){
        console.log(this);
        $(this).parent().remove();
});
SCRIPT;
    }
    private function tagOrder($tags)
    {
        $pattern = '<li class="label label-default" style="margin:4px;">%s<span class="select2-selection__clear tag_del label-warning">DEL</span><input type="hidden" name="tag_order[]" value="%s|%s"></li>';
        $html = [];
        foreach($tags as $key => $item){
            $tag = explode('|', $item);
            $html[] = sprintf($pattern, $tag[1], $tag[0], $tag[1]);
        }
        return implode('', $html);
    }
}
