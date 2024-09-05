<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Game;
use App\Models\Weather;
use App\Models\Term;

class RanksController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function showRanks (Request $request) {
        $search = $request->search;
        $students_only = $request->boolean('students_only');
        $current_term = $request->term_id ? Term::find($request->term_id) : Term::getCurrentTerm();

        $lastUpdated = Player::where('fkey_term_id', $current_term->id)
                            ->orderBy('updated_at', 'DESC')
                            ->first();
        $last_updated = $lastUpdated ? $lastUpdated->updated_at->diffForHumans() : null;

        if ($students_only){
            $student_ranks = Player::where('fkey_term_id', $current_term->id)
                                    ->where('is_student', true)
                                    ->where('rank_students', '!=', null)
                                    ->orderBy('rank_students', 'ASC');
        } else {
            $student_ranks = Player::where('fkey_term_id', $current_term->id)
                                    ->where('rank_all', '!=', null)
                                    ->orderBy('rank_all', 'ASC');
        }
        $student_ranks = $student_ranks->search($search)
                                        ->paginate(env("PAGE_NUMBER"))
                                        ->withQueryString();

        $search_action = action([RanksController::class, 'showRanks']);
        $weather = Weather::orderByDesc('created_at')->first();
        $all_terms = Term::orderBy('updated_at', 'desc')->get()
                    ->take(env('TERM_DISPLAY_NUMBER'));

        return view('adminOptions/games',
        ['data' => $student_ranks, 'ranks' => true, 'search' => $search, 'students_only' => $students_only,
            'last_updated' => $last_updated, 'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }
}
