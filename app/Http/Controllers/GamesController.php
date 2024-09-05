<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use App\Models\Player;
use App\Models\Weather;
use App\Models\Term;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class GamesController extends TemplateController
{
    public function submitScore() {
        $weather = Weather::orderByDesc('created_at')->first();
        $user = User::find(RCAuth::user()->rcid);
        $current_term = Term::getCurrentTerm();
        return view('submitScore', ['user' => $user, 'weather' => $weather, 'current_term' => $current_term]);
    }

    public function saveScore(Request $request) {
        $request->validate([
            'player1_id' => ['required', 'string', 'max:10'],
            'player2_id' => ['required', 'string', 'max:10'],
            'player1_name' => ['required', 'string', 'max:50'],
            'player2_name' => ['required', 'string', 'max:50'],
            'score1' => ['required', 'min:0'],
            'score2' => ['required', 'min:0'],
            'tourn_game' => ['nullable', 'boolean'],
            'term_id' => ['nullable', 'integer']
        ]);

        if (is_null($request->tourn_game) || $request->tourn_game) {
            // this is either a non-tournament term or a game submitted for the current tournament term.
            // game should be added to the current term.
            $submitted_to_term_id = $request->term_id;
        } else {
            // this is a tournament term and the game was submitted as a non-tournament game.
            // game should be added to the most recent non-tournament term.
            $submitted_to_term_id = Term::where('current_term', true)->where('tourn_term', false)->first()->id;
        }

        $rcid = RCAuth::user()->rcid;

        $player1 = Player::processPlayer($request->player1_id, $rcid, $submitted_to_term_id);
        $player2 = Player::processPlayer($request->player2_id, $rcid, $submitted_to_term_id);

        $game = new Game ([
            'player1_score' => $request->score1,
            'player2_score' => $request->score2,
            'fkey_player1' => $player1->id,
            'fkey_player2' => $player2->id,
            'fkey_term_id' => $submitted_to_term_id,
            'created_by' => $rcid,
            'updated_by' => $rcid
        ]);
        $game->save();

        $only_students = ($player1->is_student && $player2->is_student);
        \App\Jobs\updateRanks::dispatch($only_students, $player1, $player2, $submitted_to_term_id, $rcid);
        
        $weather = Weather::orderByDesc('created_at')->first();
        return view('scoreRecorded', ['game' => $game, 'weather' => $weather, 'submitted_to_term_id' => Term::find($submitted_to_term_id)]);
    }

    public function myGames (Request $request) {
        $search = $request->search;
        $current_term = $request->term_id ? Term::find($request->term_id) : Term::getCurrentTerm();
        $rcid = RCAuth::user()->rcid;
        $all_games = Game::where('fkey_term_id', $current_term->id)->where(function ($query) use ($rcid) {
                            $query->whereHas('player1', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    })
                                    ->orWhereHas('player2', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    });
                            })->orderBy('created_at', 'DESC');

        $all_games = $all_games->search($search)
                                ->paginate(env("PAGE_NUMBER"))
                                ->withQueryString();

        $search_action = action([GamesController::class, 'myGames']);
        $weather = Weather::orderByDesc('created_at')->first();
        $all_terms = Term::orderBy('updated_at', 'desc')->get()
                    ->take(env('TERM_DISPLAY_NUMBER'));

        return view('adminOptions/games',
        ['data' => $all_games, 'my_games' => true, 'my_rcid' => $rcid, 'search' => $search,
            'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }
}
