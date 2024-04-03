<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use App\Models\Player;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;


class GamesController extends TemplateController
{
    public function submitScore() {
        $user = User::find(RCAuth::user()->rcid);
        return view('submitScore', ['user' => $user]);
    }

    private function updateTotalNet ($player1, $player2, $score1, $score2, $only_students) {
        $diff = $score1 - $score2;

        $player1->total_net_all = $player1->total_net_all + $diff;
        $player2->total_net_all = $player2->total_net_all - $diff;

        if ($only_students) {
            $player1->total_net_students = $player1->total_net_students + $diff;
            $player2->total_net_students = $player2->total_net_students - $diff;
        }

        $player1->update();
        $player2->update();
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

        $player1 = Player::processPlayer($request->player1_id, $rcid);
        $player2 = Player::processPlayer($request->player2_id, $rcid);

        $game = new Game ([
            'player1_score' => $request->score1,
            'player2_score' => $request->score2,
            'fkey_player1' => $player1->id,
            'fkey_player2' => $player2->id,
            'created_by' => $rcid,
            'updated_by' => $rcid
        ]);

        $game->save();

        $only_students = ($player1->is_student && $player2->is_student);

        $this->updateTotalNet($player1, $player2, $game->player1_score, $game->player2_score, $only_students);

        \App\Jobs\updateRanks::dispatch($only_students);

        return view('scoreRecorded', ['game' => $game]);
    }

    public function myGames (Request $request) {
        $search = $request->search;
        $rcid = RCAuth::user()->rcid;
        $all_games = Game::where(function ($query) use ($rcid) {
                            $query->whereHas('player1', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    })
                                    ->orWhereHas('player2', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    });
                            })->orderBy('created_at', 'DESC');
        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(env("PAGE_NUMBER"))->withQueryString();
        $my_games = true;

        $search_action = action([GamesController::class, 'myGames']);
        return view('adminOptions/games',
        ['data' => $all_games, 'my_games' => $my_games, 'my_rcid' => $rcid, 'search' => $search, 'search_action' => $search_action]);
    }
}
