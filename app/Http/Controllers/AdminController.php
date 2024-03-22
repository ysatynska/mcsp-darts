<?php

namespace App\Http\Controllers;
use App\Models\Game;
use Illuminate\Http\Request;
use \App\Exports\GamesExport;

class AdminController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function allGames (Request $request) {
        $search = $request->search;
        $students_only = $request->boolean('students_only');

        $all_games = Game::orderBy('created_at', 'DESC');
        if ($students_only) {
            $all_games = $all_games->studentPlayers();
        }

        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(env("PAGE_NUMBER"))->withQueryString();
        $is_admin = true;

        $search_action = action([AdminController::class, 'allGames']);
        return view('adminoptions/games',
        ['data' => $all_games, 'is_admin' => $is_admin, 'students_only' => $students_only, 'search' => $search, 'search_action' => $search_action]);
    }

    public function exportAll () {
        $all_games = Game::orderBy('created_at','DESC')->get();
        return \Excel::download(new GamesExport($all_games), 'pingpong_all.xlsx');
    }

    public function exportStudentOnly () {
        $games_students = Game::orderBy('created_at', 'DESC')->studentPlayers()->get();
        return \Excel::download(new GamesExport($games_students), 'pingpong_students.xlsx');
    }
}
