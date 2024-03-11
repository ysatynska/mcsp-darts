<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use App\Models\Player;
use Illuminate\Database\Eloquent\SoftDeletes;
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;

class RanksController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function showRanks (Request $request) {
        $search = $request->search;
        $students_only = ($request->students_only === 'yes');
        $ranks = true;

        if (!is_null(Player::first())) {
            $last_updated = Player::orderBy('updated_at','DESC')->first()->updated_at->diffForHumans();
        } else {
            $last_updated = null;
        }

        if ($students_only){
            $student_ranks = Player::orderBy('rank_students', 'ASC')->where('is_student', true);
        } else {
            $student_ranks = Player::orderBy('rank_all', 'ASC');
        }

        if ($request->has('search')) {
            $student_ranks->search($search);
        }

        $student_ranks = $student_ranks->paginate(env("PAGE_NUMBER"))->withQueryString();

        $search_action = action([RanksController::class, 'showRanks'], ['students_only' => $request->students_only]);
        return view('adminoptions/games',
        ['data' => $student_ranks, 'ranks' => $ranks, 'search' => $search, 'students_only' => $students_only, 'last_updated' => $last_updated, 'search_action' => $search_action]);
    }
}
