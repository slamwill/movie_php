<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

// use Encore\Admin\Facades\Admin;



Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    $script = "
        <script>
            function flushCache()
            {
				swal({
					title: \"确认删除?\",
					type: \"warning\",
					showCancelButton: true,
					confirmButtonColor: \"#DD6B55\",
					confirmButtonText: \"确认\",
					showLoaderOnConfirm: true,
					cancelButtonText: \"取消\",
					preConfirm: function() {
						return new Promise(function(resolve) {
							$.ajax({
								method: 'get',
								url: '".route('admin.api.flushCache')."',
								success: function (response) {
									swal('清除緩存成功', '前端RedisCache已清空', 'success'); 
								},
								error: function () {
									swal('清除緩存失敗', '請連絡管理員', 'error'); 
								}
							});
						});
					}
				});

            }
        </script>
    ";

    $navbar->right('    <li>
        <a href = "javascript:flushCache()">清除緩存</a>
    </li>'.$script);

});


Encore\Admin\Form::forget(['map', 'editor']);

Admin::css('/css/admin.globals.css');

Admin::css('https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css');
Admin::js('https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js');

Admin::css('/css/jquery.mloading.css');
Admin::js('/js/jquery.mloading.js');

Admin::js('/js/jquery.ui.js');







Admin::css('https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/2.0.4/css/Jcrop.min.css');
Admin::js('https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/2.0.4/js/Jcrop.min.js');


Admin::css('/js/daterangepicker/daterangepicker-bs3.css');
Admin::js('/js/moment.min.js');
Admin::js('/js/daterangepicker/daterangepicker.js');
Admin::js('/js/common.js');