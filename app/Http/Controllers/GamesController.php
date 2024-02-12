<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class GamesController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function submitScore() {
        $user = User::find(RCAuth::user()->rcid);

        return view('submitScore', ['user' => $user]);
    }

    public function saveScore(Request $request) {
        $request->validate([
            'player1_id' => ['required', 'string', 'max:10'],
            'player2_id' => ['required', 'string', 'max:10'],
            'player1_name' => ['required', 'string', 'max:50'],
            'player2_name' => ['required', 'string', 'max:50'],
            'score1' => ['required', 'min:0'],
            'score2' => ['required', 'min:0'],
        ]);

        $rcid = RCAuth::user()->rcid;
        $game = new Game ([
            'player1_rcid' => $request->player1_id,
            'player2_rcid' => $request->player2_id,
            'player1_score' => $request->score1,
            'player2_score' => $request->score2,
            'player1_name' => $request->player1_name,
            'player2_name' => $request->player2_name,
            'created_by' => $rcid,
            'updated_by' => $rcid
        ]);

        $game->save();

        return redirect()->action([GamesController::class, 'scoreRecorded'], ['game' => $game]);
    }

    public function scoreRecorded(Game $game){
        return view('scoreRecorded', ['game' => $game]);
    }

    public function myGames (Request $request) {
        $search = $request->search;
        $rcid = RCAuth::user()->rcid;

        $all_games = Game::where('player1_rcid', $rcid)
                        ->orWhere('player2_rcid', $rcid)
                        ->orderBy('created_at', 'DESC');

        if ($request->has('search')) {
            $all_games->search($search);
        }

        $all_games = $all_games->paginate(14)->withQueryString();

        $my_games = true;
        return view('adminoptions/allGames',
        ['all_games' => $all_games, 'my_games' => $my_games, 'my_rcid' => $rcid, 'search' => $search]);
    }
}
