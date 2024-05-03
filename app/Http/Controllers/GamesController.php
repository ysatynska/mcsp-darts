<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use App\Models\Player;
use App\Models\Weather;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;


class GamesController extends TemplateController
{
    public function submitScore() {
        $rcid = RCAuth::user()->rcid;
        $weather = Weather::orderByDesc('created_at')->first();
        $user = User::find($rcid);
        $all_terms = Game::orderBy('created_at', 'desc')->pluck('term')->unique();
        $current_term = Game::orderBy('created_at', 'desc')->first()->term;
        return view('submitScore', ['user' => $user, 'weather' => $weather, 'is_admin' => in_array($rcid, config('app.admin_users', [])), 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }

    private function getCurrentTerm(Request $request) {
        if ($request->term) {
            // term is not null
            if (!is_null($request->tourn_game)) {
                if (!$request->tourn_game) {
                    // tourn_game is no, term is not null
                    if (substr($request->term, -1) === 'T') {
                        $validator = Validator::make([], []);
                        $validator->errors()->add('term', 'You cannot submit a non-tournament game for a tournament term.');
                        throw new ValidationException($validator);
                    } else {
                        return [$request->term, null];
                    }
                } else {
                    // tourn_game is yes, term is not null
                    $validator = Validator::make([], []);
                    $validator->errors()->add('tourn_game', 'You cannot submit a tournament game for a term other than the current one.');
                    throw new ValidationException($validator);
                }
            } else {
                // tourn_game is null, term is not null
                return [$request->term, null];
            }
        } else {
            // term is null
            if (!is_null($request->tourn_game)) {
                if ($request->tourn_game) {
                    // tourn_game is yes, term is null
                    return [Game::orderBy('created_at', 'desc')->first()->term, null];
                } else {
                    // tourn_game is no, term is null
                    // need to find the latest term that is not a tournament term
                    $skip = 1;
                    while (true) {
                        $current_term = Game::orderBy('created_at', 'desc')->skip($skip)->first();
                        if (substr($current_term->term, -1) != 'T') {
                            return [$current_term->term, $current_term->created_at];
                        }
                        $skip++;
                    }
                }
            } else {
                // tourn_game is null, term is null
                return [Game::orderBy('created_at', 'desc')->first()->term, null];
            }
        }
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
            'term' => ['nullable', 'regex:/^\d{4}-\d{4}[T]?$/']
        ]);

        [$current_term, $created_at] = $this->getCurrentTerm($request);

        $rcid = RCAuth::user()->rcid;

        $player1 = Player::processPlayer($request->player1_id, $rcid, $current_term);
        $player2 = Player::processPlayer($request->player2_id, $rcid, $current_term);

        $game = new Game ([
            'player1_score' => $request->score1,
            'player2_score' => $request->score2,
            'fkey_player1' => $player1->id,
            'fkey_player2' => $player2->id,
            'term' => $current_term,
            'created_by' => $rcid,
            'updated_by' => $rcid
        ]);
        if ($created_at) {
            $game->created_at = $created_at;
        }
        $game->save();

        $only_students = ($player1->is_student && $player2->is_student);
        // Player::updateRanks($only_students, $player1, $player2, $current_term);
        \App\Jobs\updateRanks::dispatch($only_students, $player1, $player2, $current_term);

        $weather = Weather::orderByDesc('created_at')->first();
        return view('scoreRecorded', ['game' => $game, 'weather' => $weather]);
    }

    public function myGames (Request $request) {
        $search = $request->search;
        $current_term = $request->term ? $request->term : Game::orderBy('created_at', 'desc')->first()->term;
        $rcid = RCAuth::user()->rcid;
        $all_games = Game::where('term', $current_term)->where(function ($query) use ($rcid) {
                            $query->whereHas('player1', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    })
                                    ->orWhereHas('player2', function ($query) use ($rcid) {
                                        $query->where('rcid', $rcid);
                                    });
                            })->orderBy('updated_at', 'DESC');
        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(env("PAGE_NUMBER"))->withQueryString();
        $my_games = true;

        $search_action = action([GamesController::class, 'myGames']);
        $weather = Weather::orderByDesc('created_at')->first();
        $all_terms = Game::orderBy('created_at', 'desc')
                    ->pluck('term')
                    ->unique()
                    ->take(env('TERM_DISPLAY_NUMBER'));

        return view('adminOptions/games',
        ['data' => $all_games, 'my_games' => $my_games, 'my_rcid' => $rcid, 'search' => $search,
            'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }
}
