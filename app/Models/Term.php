<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $table = "AcademicAffairsOperations.mcsp_pingpong.terms";
    protected $primaryKey = "id";

    protected $fillable = ['term_name', 'tourn_term', 'current_term', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];

    public static function getCurrentTerm () {
        // if current term is a tournament term, two terms will have current = true -
        //   the tournament one and the most recent non-tournament one
        $current_terms = Term::where('current_term', true)->get();
        if (count($current_terms) > 1) {
            return Term::where('current_term', true)->where('tourn_term', true)->first();
        } else {
            return $current_terms->first();
        }
    }
}
