<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Game;
use App\Models\Weather;

class RanksController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function showRanks (Request $request) {
        $search = $request->search;
        $students_only = $request->boolean('students_only');
        $current_term = $request->term ? $request->term : Game::orderBy('created_at', 'desc')->first()->term;
        $ranks = true;

        $lastUpdated = Player::where('term', $current_term)
                            ->orderBy('updated_at', 'DESC')
                            ->first();
        $last_updated = $lastUpdated ? $lastUpdated->updated_at->diffForHumans() : null;

        if ($students_only){
            $student_ranks = Player::where('term', $current_term)
                                    ->where('is_student', true)
                                    ->where('rank_students', '>=', 0)
                                    ->where('rank_students', '!=', null)
                                    ->orderBy('rank_students', 'ASC');
        } else {
            $student_ranks = Player::where('term', $current_term)
                                    ->where('rank_all', '>=', 0)
                                    ->orderBy('rank_all', 'ASC');
        }
        if ($request->has('search')) {
            $student_ranks->search($search);
        }
        $student_ranks = $student_ranks->paginate(env("PAGE_NUMBER"))->withQueryString();
        $weather = Weather::orderByDesc('created_at')->first();

        $search_action = action([RanksController::class, 'showRanks'], ['students_only' => $request->students_only]);
        $all_terms = Game::orderBy('created_at', 'desc')
                    ->pluck('term')
                    ->unique()
                    ->take(env('TERM_DISPLAY_NUMBER'));

        return view('adminOptions/games',
        ['data' => $student_ranks, 'ranks' => $ranks, 'search' => $search, 'students_only' => $students_only,
            'last_updated' => $last_updated, 'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }
}
