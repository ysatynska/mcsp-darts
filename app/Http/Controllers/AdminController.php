<?php

namespace App\Http\Controllers;
use App\Models\Game;
use Illuminate\Http\Request;
use \App\Exports\GamesExport;
use App\Models\Weather;

class AdminController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function allGames (Request $request) {
        $weather = Weather::orderByDesc('created_at')->first();
        $search = $request->search;
        $students_only = $request->boolean('students_only');
        $current_term = $request->term ? $request->term : Game::orderBy('created_at', 'desc')->first()->term;

        $all_games = Game::where('term', $current_term)->orderBy('updated_at', 'DESC');
        if ($students_only) {
            $all_games = $all_games->studentPlayers();
        }

        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(env("PAGE_NUMBER"))->withQueryString();
        $is_admin = true;

        $search_action = action([AdminController::class, 'allGames']);
        $all_terms = Game::orderBy('created_at', 'desc')
                    ->pluck('term')
                    ->unique()
                    ->take(config('app.term_display_number'));

        return view('adminOptions/games',
        ['data' => $all_games, 'is_admin' => $is_admin, 'students_only' => $students_only, 'search' => $search,
            'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }

    public function exportAll (Request $request) {
        $all_games = Game::where('term', $request->term)->orderBy('created_at','DESC')->get();
        return \Excel::download(new GamesExport($all_games), 'pingpong_all_'.$request->term.'.xlsx');
    }

    public function exportStudentOnly (Request $request) {
        $games_students = Game::where('term', $request->term)->orderBy('created_at', 'DESC')->studentPlayers()->get();
        return \Excel::download(new GamesExport($games_students), 'pingpong_students_'.$request->term.'.xlsx');
    }
}
