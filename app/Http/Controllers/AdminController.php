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
        $students_only = ($request->students_only === 'yes');

        if ($students_only) {
            $all_games = Game::whereHas('player1', function ($query) {
                                $query->where('is_student', true)->withoutGlobalScopes();
                            })
                            ->whereHas('player2', function ($query) {
                                $query->where('is_student', true)->withoutGlobalScopes();
                            })->orderBy('created_at', 'DESC');
        } else {
            $all_games = Game::orderBy('created_at', 'DESC');
        }

        if ($request->has('search')) {
            $all_games->search($search);
        }
        $all_games = $all_games->paginate(14)->withQueryString();
        $is_admin = true;

        return view('adminoptions/games',
        ['data' => $all_games, 'is_admin' => $is_admin, 'students_only' => $students_only, 'search' => $search]);
    }

    public function exportAll () {
        $all_games = Game::orderBy('created_at','DESC')->get();
        return \Excel::download(new GamesExport($all_games), 'pingpong_all.xlsx');
    }

    public function exportStudentOnly () {
        $games_students = Game::whereHas("player1", function ($query) {
                                    $query->where('is_student', true)->withoutGlobalScopes();
                                })
                                ->whereHas('player2', function ($query) {
                                    $query->where('is_student', true)->withoutGlobalScopes();
                                })
                                ->get();
        return \Excel::download(new GamesExport($games_students), 'pingpong_students.xlsx');
    }
}
