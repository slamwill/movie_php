<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GamesController extends Controller
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

    public function play($gameId)
    {
		list($gameId, $gameType) = explode('-', $gameId);

		return view( 'games', ['gameId' => $gameId, 'gameType' => $gameType] );
    }

	//新視窗
    public function playBlank($gameId)
    {
		list($gameId, $gameType) = explode('-', $gameId);

		return view( 'games-new-windows', ['gameId' => $gameId, 'gameType' => $gameType] );
    }




}
