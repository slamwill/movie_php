<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


		//$AvVideos = \App\AvVideo::where('enable','off')->orderBy('updated_at','desc')->paginate(16);
		$AvVideos = \App\AvVideo::orderBy('updated_at','desc')->paginate(16);

		$links = $AvVideos->links();
		$AvVideos = $AvVideos->toArray();

		foreach ($AvVideos['data'] as &$video){
			$video['actros_name'] =  array();
			if ($video['actors']) {
				$video['actors_name'] =	\App\AvActor::whereIn('id',explode(',', $video['actors']))->get()->pluck('name')->toArray();
			}
			if ($video['tags']) {
				$video['tags_name'] = \App\AvTag::whereIn('id',explode(',', $video['tags']))->get()->pluck('name')->toArray();
			}
		}


		
		return view('index',['AvVideos' => $AvVideos, 'links' => $links]);
    }
}
