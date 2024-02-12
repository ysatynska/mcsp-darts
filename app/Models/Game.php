<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Game extends Model
{
    protected $table = "ysatynska_training.dbo.pp_games_ys";
    protected $primaryKey = "id";

    protected $fillable = ['player1_rcid', 'player2_rcid', 'player1_score', 'player2_score', 'fkey_doubles', 'created_by', 'updated_by', 'player1_name', 'player2_name'];
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
                    $query->where('player1_name', 'LIKE', sprintf('%%%s%%', $search_term))
                          ->orWhere('player2_name', 'LIKE', sprintf('%%%s%%', $search_term));
                });
            }

        }
      }

      public function player1 () {
        return $this->hasOne(User::class, 'RCID', 'player1_rcid');
      }

      public function player2 () {
        return $this->hasOne(User::class, 'RCID', 'player2_rcid');
      }
}
