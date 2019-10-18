<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/api/nuxt/latest','ApiVideoController@latest');
Route::get('/api/once/video','ApiVideoController@onceVideo');

Route::get('/api/censored', 'ApiVideoController@hdCensored'); // 有碼
Route::get('/api/uncensored', 'ApiVideoController@hdUncensored'); // 無碼
Route::get('/api/united', 'ApiVideoController@hdUnited'); // 歐美
Route::get('/api/cartoon', 'ApiVideoController@hdCartoon'); // 卡通
Route::get('/api/self', 'ApiVideoController@hdSelf'); // 自拍

Route::get('/api/search/{keyword}', 'ApiVideoController@search')->name('search');
Route::get('/api/tag/{string}', 'ApiVideoController@tag')->name('tag')->where('string', '(.*)');
//Route::get('/tag/{string}', 'VideoController@tag')->name('tag')->where('string', '(.*)');



// Route::get('/api/censored', 'ApiVideoController@hdCensored')->name('api.censored'); // 有碼
// Route::get('/api/uncensored', 'ApiVideoController@hdUncensored')->name('api.uncensored'); // 無碼
// Route::get('/api/united', 'ApiVideoController@hdUnited')->name('api.united'); // 歐美
// Route::get('/api/cartoon', 'ApiVideoController@hdCartoon')->name('api.cartoon'); // 卡通
// Route::get('/api/self', 'ApiVideoController@hdSelf')->name('api.self'); // 自拍





//happypay验证
Route::post('/api/v1/happypay/CheckOrder','\\App\\Payment\\Happypay@order');
Route::get('/api/v1/happypay/Callback','\\App\\Payment\\Happypay@callback');

//feibaopay验证
Route::post('/api/feibaopay/Callback','\\App\\Payment\\Feibaopay@callback');
//Route::get('/api/feibaopay/Callback','\\App\\Payment\\Feibaopay@callback');

//bifubaopay验证
Route::post('/api/bifubaopay/Callback','\\App\\Payment\\BiFubaopay@callback');
//Route::get('/api/bifubaopay/Callback','\\App\\Payment\\BiFubaopay@callback');

//nnexpay验证
Route::post('/api/nnexpay/Callback','\\App\\Payment\\Nnexpay@callback');


Route::any('/goto/vip',function (){
	return redirect('/user/vip');
});




Route::middleware('auth')->prefix('user')->group(function () {

	Route::get('/password', 'UserController@password')->name('user.password');
	Route::post('/password', 'UserController@passwordUpdate');



	Route::get('/videos', 'UserController@videos')->name('user.videos');
	Route::get('/actors', 'UserController@actors')->name('user.actors');
	Route::get('/watches', 'UserController@watches')->name('user.watches');
	Route::get('/personal', 'UserController@personal')->name('user.personal');
	Route::post('/personal', 'UserController@passwordUpdate');
	Route::post('/emailUpdate', 'UserController@emailUpdate');

	Route::get('/transfer', 'UserController@transfer')->name('user.transfer');

	//Route::get('/recharge', 'UserController@recharge')->name('user.recharge');
	// Happypay 使用
	Route::get('/recharge', 'UserController@recharge')->name('user.recharge');
	Route::post('/recharge', 'UserController@rechargeOrder')->name('user.recharge.order');

	// 肥寶支付 使用
	Route::post('/fbRecharge', 'UserController@fbRechargeOrder')->name('user.fbRecharge.order');
	Route::get('/fbRecharge', 'UserController@fbRecharge')->name('user.fbRecharge');

	// 必付寶支付 使用
	Route::post('/bfRecharge', 'UserController@bfRechargeOrder')->name('user.bfRecharge');




	// 必付寶支付 使用
	Route::post('/nnexRecharge', 'UserController@nnexRechargeOrder')->name('user.nnexRecharge');







	Route::get('/vip', 'UserController@vip')->name('user.vip');
	Route::post('/vip', 'UserController@transferVIP');//確定消費VIP方案


});

Route::middleware('pageview')->prefix('games')->group(function () {

	Route::get('/{gameId}/play', 'GamesController@play')->where('gameId', '[A-Za-z0-9_\-]+')->name('games.play');
	Route::get('/{gameId}/play/blank', 'GamesController@playBlank')->where('gameId', '[A-Za-z0-9_\-]+')->name('games.play.blank');

});

//Route::middleware('pageview')->get('/login/popup', 'Auth\LoginController@loginPopup')->name('login.popup');
//Route::middleware('pageview')->get('/register/popup', 'Auth\RegisterController@registerPopup')->name('register.popup');

//免費影片 列表
Route::middleware('pageview')->get('/list/{string}/free', 'VideoController@listFree')->name('list.free');

Route::middleware('pageview')->get('/banner/{string}', 'VideoController@banner')->name('banner');

//HD影片
Route::middleware('pageview')->group(function () {
    Route::get('/latest', 'VideoController@hdLatest')->name('latest'); // 最新影片
    Route::get('/watch/{avkey}', 'VideoController@watch')->where('avkey', '[A-Za-z0-9_\-]+')->name('watch');
    Route::get('/actor/{string}', 'VideoController@actor')->name('actor');
    Route::get('/self', 'VideoController@hdSelf')->name('self'); // 自拍
	Route::get('/united', 'VideoController@hdUnited')->name('united'); // 歐美
    Route::get('/cartoon', 'VideoController@hdCartoon')->name('cartoon'); // 卡通
	Route::get('/free', 'VideoController@hdFree')->name('free'); // 免費
    Route::get('/uncensored', 'VideoController@hdUncensored')->name('uncensored');
    Route::get('/censored', 'VideoController@hdCensored')->name('censored');
    Route::get('/tag/{string}', 'VideoController@tag')->name('tag')->where('string', '(.*)');
});


Route::middleware('pageview')->get('/japan', 'VideoController@japan')->name('japan');
Route::middleware('pageview')->get('/search/{keyword}', 'VideoController@search')->name('search');
Route::middleware('pageview')->get('/', 'VideoController@index')->name('home'); //首頁
Route::middleware('pageview')->get('/help', 'VideoController@help')->name('help'); //幫助中心


Route::middleware('pageview')->get('/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('forgot'); //忘記密碼
Route::middleware('pageview')->get('/sendResetLinkEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('sendResetLinkEmail'); //驗證密碼



Auth::routes();
Route::middleware('pageview')->get('/login/refereshcapcha', 'Auth\LoginController@refereshcapcha');
Route::middleware('pageview')->get('/home', 'VideoController@index');

Route::middleware('pageview')->get('/getVideoInfo', 'VideoController@getVideoInfo')->name('getVideoInfo');
Route::middleware('pageview')->get('/getVideoCarInfo', 'VideoController@getVideoCarInfo')->name('getVideoCarInfo');

//Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('pageview')->prefix('category')->group(function () {
	Route::get('/', 'VideoController@category')->name('category');
});




Route::group(['prefix' => 'api', 'namespace' => 'Api'], function () {


	Route::post('/collection/actors', 'CollectionController@actors');
	// Route::post('/collection/videos', 'CollectionController@videos');
	Route::post('/collection/videos', 'CollectionController@videos')->name('api.collection.videos');
	Route::post('/collection/watchs', 'CollectionController@watchs');


	Route::get('/latest', 'VideoController@latest')->name('api.latest');


	Route::get('/{avkey}/play.m3u8', 'VideoController@play')->name('api.play');
	Route::get('/admin/{avkey}/play', 'VideoController@AdminPlay')->name('api.admin.play');

	
	Route::post('/{avkey}/downloadConfirm', 'VideoController@downloadConfirm')->name('api.downloadConfirm');
	Route::get('/{avkey}/download', 'VideoController@download')->name('api.download');

	Route::get('/{avkey}/consume', 'VideoController@consume')->name('api.consume');
	
	//通知影片到位
	Route::post('/video/{avkey}/notify', 'VideoController@notify')->name('api.notify');



	Route::get('/TempMonkey/{avkey}/Check', 'VideoController@TempMonkey');

	
	//驗證下載是否合法
	Route::get('/download/verification', 'VideoController@downloadVerification')->name('api.downloadVerification');



	//$router->resource('video', VideoController::class);
	Route::get('/touchSwitchMenu/{switch}', 'VideoController@touchSwitchMenu')->where('switch', '[0-1]');


});

