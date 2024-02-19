<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Player extends Model
{
    protected $table = "ysatynska_training.dbo.pp_players_ys";
    protected $primaryKey = "id";

    protected $fillable = ['name', 'rcid', 'net_points', 'is_student', 'rank', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];

    public function user () {
        return $this->hasOne(User::class, 'RCID', 'rcid');
    }

    public function processPlayer($player_rcid, $submitter_rcid) {
        $player = Player::where('rcid', $player_rcid)->first();
        if (empty($player->id)) {
            $user = User::find($player_rcid);
            $player = new Player ([
                'name' => $user->display_name,
                'rcid' => $player_rcid,
                'is_student' => ($user->Student === 'Yes'),
                'created_by' => $submitter_rcid,
                'updated_by' => $submitter_rcid
            ]);
            $player->save();
        }
        return $player;
    }

    public function scopeSearch(Builder $query, $search_term) {
        if (!empty($search_term)) {
            if (is_numeric($search_term)) {
                $query->where(function ($query) use ($search_term) {
                    $query->where('net_points', $search_term)
                          ->orWhere('rank', $search_term);
                });
            } else {
                $query->where(function ($query) use ($search_term) {
                    $query->where('name', 'LIKE', sprintf('%%%s%%', $search_term));
                });
            }
        }
      }
}
