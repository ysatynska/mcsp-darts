<?php

namespace App\Http\Controllers;
use App\Models\Game;
use App\Models\Term;
use Illuminate\Http\Request;
use \App\Exports\GamesExport;
use App\Models\Weather;

use RCAuth;

class AdminController extends TemplateController
{
    public function __construct(){
        parent::__construct();
    }

    public function allGames (Request $request) {
        $search = $request->search;
        $students_only = $request->boolean('students_only');
        $current_term = $request->term_id ? Term::find($request->term_id) : Term::getCurrentTerm();
        $rcid = RCAuth::user()->rcid;

        $all_games = Game::where('fkey_term_id', $current_term->id)->orderBy('created_at', 'DESC');
        if ($students_only) {
            $all_games = $all_games->studentPlayers();
        }
        $all_games = $all_games->search($search)
                                ->paginate(env("PAGE_NUMBER"))
                                ->withQueryString();

        $search_action = action([AdminController::class, 'allGames']);
        $weather = Weather::orderByDesc('created_at')->first();
        $all_terms = Term::orderBy('updated_at', 'desc')->get()
                    ->take(config('app.term_display_number'));

        return view('adminOptions/games',
        ['data' => $all_games, 'is_admin' => in_array($rcid, config('app.admin_users', [])), 'students_only' => $students_only, 'search' => $search,
            'search_action' => $search_action, 'weather' => $weather, 'all_terms' => $all_terms, 'current_term' => $current_term]);
    }

    public function manageTerms () {
        $weather = Weather::orderByDesc('created_at')->first();

        $all_terms = Term::orderBy('updated_at', 'desc')->get();
        $current_term = Term::getCurrentTerm();
        return view('adminOptions/manageTerms', ['weather' => $weather, 'current_term' => $current_term, 'all_terms' => $all_terms]);
    }

    private function updateCurrentTerm (Term $term) {
        $current_term = Term::getCurrentTerm();
        if ($current_term->tourn_term) {
            if ($term->tourn_term) {
                $current_term->current_term = false;
                $current_term->updated_by = RCAuth::user()->rcid;
                $current_term->save();
            } else {
                $current_terms = Term::where('current_term', true)->get();
                foreach ($current_terms as $current) {
                    $current->current_term = false;
                    $current->updated_by = RCAuth::user()->rcid;
                    $current->save();
                }
            }
        } else {
            if (!$term->tourn_term) {
                $current_term->current_term = false;
                $current_term->updated_by = RCAuth::user()->rcid;
                $current_term->save();
            }
        }
    }

    public function changeCurrentTerm (Request $request) {
        $request->validate([
            'old_term' => ['nullable', 'integer'],
            'tourn_term' => ['nullable', 'boolean'],
            'new_term_name' => ['nullable', 'string', 'max:255'],
        ]);
        if ($request->old_term) {
            $term = Term::find($request->old_term);
            $term->current_term = true;
            $term->updated_by = RCAuth::user()->rcid;
        } else {
            $term = new Term([
                'tourn_term' => $request->boolean('tourn_term'),
                'term_name' => $request->new_term_name,
                'current_term' => true,
                'created_by' => RCAuth::user()->rcid,
                'updated_by' => RCAuth::user()->rcid
            ]);
        }
        $this->updateCurrentTerm($term);
        $term->save();
        return redirect()->action([AdminController::class, 'manageTerms']);
    }

    public function exportAll (Request $request) {
        $term_exported = Term::find($request->term_id);
        $term_string = $term_exported->term_name.($term_exported->tourn_term ? 'tourn':'');
        $all_games = Game::where('fkey_term_id', $request->term_id)->orderBy('created_at','DESC')->get();
        return \Excel::download(new GamesExport($all_games, $term_string), 'pingpong_all_'.$term_string.'.xlsx');
    }

    public function exportStudentOnly (Request $request) {
        $term_exported = Term::find($request->term_id);
        $term_string = $term_exported->term_name.($term_exported->tourn_term ? 'tourn':'');
        $games_students = Game::where('fkey_term_id', $request->term_id)->orderBy('created_at', 'DESC')->studentPlayers()->get();
        return \Excel::download(new GamesExport($games_students, $term_string), 'pingpong_students_'.$term_string.'.xlsx');
    }
}
