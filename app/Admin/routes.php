<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

	$router->get('/api/actors', 'AvVideoController@actors');
	$router->get('/api/tags', 'AvVideoController@tags');
	$router->get('/api/reportService', 'ReportServiceController@customGridTab3');

	$router->post('/api/crop', 'AvVideoController@crop')->name('admin.api.crop');
	$router->post('/api/fetchCoverImage', 'AvVideoController@fetchCoverImage')->name('admin.api.fetchCoverImage');
	$router->post('/api/fetchActorImage', 'AvVideoController@fetchActorImage')->name('admin.api.fetchActorImage');
	$router->get('/api/flushCache', 'AvVideoController@flushCache')->name('admin.api.flushCache');
    $router->post('/api/storeOrder', 'AvTagorderController@storeOrder')->name('admin.api.storeOrder');
	/*
	無用
	$router->post('/ReportCategory', 'ReportCategoryController@index')->name('admin.api.reportCategoryRange');
    $router->post('/ReportTag', 'ReportTagController@index')->name('admin.api.reportTagRange');
    $router->post('/ReportWatch', 'ReportWatchController@index')->name('admin.api.reportWatchRange');
    $router->post('/RecountReports', 'RecountReportsController@index')->name('admin.api.recountReports');
	*/
	$router->resource('AvActor', AvActorController::class);
	$router->resource('AvTag', AvTagController::class);
	$router->resource('AvVideo', AvVideoController::class);
	$router->resource('User', UserController::class);
	$router->resource('Banner', BannerController::class);
    $router->resource('AvTagorder', AvTagorderController::class);
    $router->resource('ReportWatch', ReportWatchController::class);
    $router->resource('ReportTag', ReportTagController::class);
    $router->resource('ReportCategory', ReportCategoryController::class);
    $router->resource('ReportMonth', ReportMonthController::class);
    $router->resource('ReportRecount', RecountReportsController::class);

	$router->post('/api/spider', 'AvVideoController@spider');
	$router->get('/api/video', 'AvVideoController@video')->name('admin.api.video');


	$router->resource('RechargeConfig', RechargeConfigController::class);
	$router->resource('ServiceConfig', ServiceConfigController::class);	

	$router->resource('ReportRecharge', ReportRechargeController::class);
	$router->resource('ReportService', ReportServiceController::class);	

});
