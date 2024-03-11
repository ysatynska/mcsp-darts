<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Player;

class Game extends Model
{
    protected $table = "ysatynska_training.dbo.pp_games_ys";
    protected $primaryKey = "id";

    protected $fillable = ['fkey_player1', 'fkey_player2', 'player1_score', 'player2_score', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];

    public function scopeSearch(Builder $query, $search_term) {
        if (!empty($search_term)) {
            if (is_numeric($search_term)) {
                $query->where(function ($query) use ($search_term) {
                    $query->where('player1_score', $search_term)
                          ->orWhere('player2_score', $search_term);
                });
            } else {
                $query->where(function ($query) use ($search_term) {
                    $query->whereHas('player1', function ($query) use ($search_term) {
                        $query->where('name', 'LIKE', sprintf('%%%s%%', $search_term));
                    })
                    ->orWhereHas('player2', function ($query) use ($search_term) {
                        $query->where('name', 'LIKE', sprintf('%%%s%%', $search_term));
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

      public function scopeStudentPlayers (Builder $query) {
        $query->whereHas('player1', function ($query) {
            $query->where('is_student', true);
        })
        ->whereHas('player2', function ($query) {
            $query->where('is_student', true);
        });
      }
}
