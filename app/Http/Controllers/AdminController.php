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
        $all_games = Game::query()->orderBy('created_at', 'DESC');

        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(14)->withQueryString();
        $is_admin = true;

        return view('adminoptions/games', ['data' => $all_games, 'is_admin' => $is_admin, 'search' => $search]);
    }

    public function exportAll () {
        $all_games = Game::get();
        return \Excel::download(new GamesExport($all_games), 'Ping_Pong_Games_Data.xlsx');
    }

    public function exportStudentOnly () {
        $games_students = Game::whereHas("player1", function ($query) {
                                    $query->where('is_student', true)->withoutGlobalScopes();
                                })
                                ->whereHas('player2', function ($query) {
                                    $query->where('is_student', true)->withoutGlobalScopes();
                                })
                                ->get();

        return \Excel::download(new GamesExport($games_students), 'Ping_Pong_All_Games_Data.xlsx');
    }
}
