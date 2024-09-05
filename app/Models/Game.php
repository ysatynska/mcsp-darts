<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Player;
use App\Models\Term;

class Game extends Model
{
    protected $table = "AcademicAffairsOperations.mcsp_pingpong.games";
    protected $primaryKey = "id";

    protected $fillable = ['fkey_player1', 'fkey_player2', 'player1_score', 'player2_score', 'fkey_term_id', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];
    protected $with = ['player1', 'player2', 'term'];

    public function scopeSearch(Builder $query, $search_term) {
        if (!empty($search_term)) {
            if (is_numeric($search_term)) {
                $query->where(function ($query) use ($search_term) {
                    $query->where('player1_score', $search_term)
                          ->orWhere('player2_score', $search_term);
                });
            } else {
                $query->where(function ($query) use ($search_term) {
                    $query->whereHas('player1.user', function ($query) use ($search_term) {
                        $query->where('rc_full_name', 'LIKE', sprintf('%%%s%%', $search_term));
                    })
                    ->orWhereHas('player2.user', function ($query) use ($search_term) {
                        $query->where('rc_full_name', 'LIKE', sprintf('%%%s%%', $search_term));
                    });
                });
            }
        }
      }

      public function player1 () {
        return $this->hasOne(Player::class, 'id', 'fkey_player1');
      }

      public function player2 () {
        return $this->hasOne(Player::class, 'id', 'fkey_player2');
      }

      public function term () {
        return $this->hasOne(Term::class, 'id', 'fkey_term_id');
      }

      public function scopeStudentPlayers (Builder $query) {
        $query->whereHas('player1', function ($query) {
            $query->where('is_student', true);
        })
        ->whereHas('player2', function ($query) {
            $query->where('is_student', true);
        });
      }
}
