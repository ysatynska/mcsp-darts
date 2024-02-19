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

        $last_updated = Player::orderBy('updated_at','DESC')->first()->updated_at->diffForHumans();
        // ddd($last_updated->updated_at->format('g:ma'));
        if ($students_only){
            $student_ranks = Player::orderBy('rank_students', 'ASC')->where('is_student', true);
        } else {
            $student_ranks = Player::orderBy('rank_all', 'ASC');
        }

        if ($request->has('search')) {
            $student_ranks->search($search);
        }

        $student_ranks = $student_ranks->paginate(14)->withQueryString();

        return view('adminoptions/games',
        ['data' => $student_ranks, 'ranks' => $ranks, 'search' => $search, 'students_only' => $students_only, 'last_updated' => $last_updated]);
    }
}
