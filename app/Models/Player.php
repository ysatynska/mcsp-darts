<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;

class Player extends Model
{
    protected $table = "ysatynska_training.dbo.pp_players_ys";
    protected $primaryKey = "id";

    protected $fillable = ['name', 'rcid', 'net_points', 'is_student', 'rank', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];

    public function user () {
        return $this->hasOne(User::class, 'RCID', 'rcid');
    }

    public static function processPlayer($player_rcid, $submitter_rcid) {
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

    private static function getRREF($players, $only_students) {
        foreach ($players as $player) {
            $total_num_games = Game::where(function ($query) use ($player) {
                                        $query->where('fkey_player1', $player->id)
                                            ->orWhere('fkey_player2', $player->id);
                                    });
            if ($only_students) {
                $total_num_games = $total_num_games->where(function ($query) {
                                                        $query->whereHas('player1', function ($query) {
                                                            $query->where('is_student', true);
                                                        })
                                                        ->whereHas('player2', function ($query) {
                                                            $query->where('is_student', true);
                                                        });
                                                    });
            }
            $total_num_games = $total_num_games->count();

            $player_net_points = ($only_students ? $player->total_net_students : $player->total_net_all);
            $one_player_array = array();
            foreach ($players as $opponent) {
                if ($opponent->id !== $player->id){
                    $num_games_opponent = Game::where(function ($query) use ($player, $opponent){
                                                $query->where('fkey_player1', $player->id)
                                                    ->where('fkey_player2', $opponent->id);
                                            })
                                            ->orWhere(function ($query) use ($player, $opponent) {
                                                $query->where('fkey_player2', $player->id)
                                                    ->where('fkey_player1', $opponent->id);
                                            })->count();
                    $one_player_array[] = (-1) * $num_games_opponent;
                } else {
                    $one_player_array[] = $total_num_games;
                }
            }
            $one_player_array[] = $player_net_points;
            $matrix[] = $one_player_array;
        }
        $math_matrix = MatrixFactory::create($matrix);
        return ($math_matrix->rref());
    }

    private static function storeRanks ($ranks, $players, $only_students) {
        $index = 0;
        if ($only_students){
            foreach ($players as $player) {
                $player->rank_students = intval($ranks[$index]);
                $index+=1;
                $player->update();
            }
        } else {
            foreach ($players as $player) {
                $player->rank_all = intval($ranks[$index]);
                $index+=1;
                $player->update();
            }
        }
    }
    private static function addMinValue ($array) {
        $min = (-1) * min($array);
        foreach ($array as $key=>$item) {
            $array[$key] += $min;
        }
        return $array;
    }

    private static function convertToRanks ($row_ranks) {
        $ranks = array();
        $ordered_ranks = $row_ranks;
        rsort($ordered_ranks);
        foreach ($row_ranks as $key => $value) {
            foreach ($ordered_ranks as $ordered_key => $ordered_value) {
                if ($value === $ordered_value) {
                    $key = $ordered_key;
                    break;
                }
            }
            $ranks[] = intval($key+1);
        }
        return $ranks;
    }

    private static function storeRatings ($ratings, $players, $only_students) {
        $index = 0;
        if ($only_students){
            foreach ($players as $player) {
                $player->rating_students = intval(round($ratings[$index]));
                $index+=1;
                $player->update();
            }
        } else {
            foreach ($players as $player) {
                $player->rating_all = intval(round($ratings[$index]));
                $index+=1;
                $player->update();
            }
        }
    }

    private static function calculateRanks ($players, $only_students) {
        $rref = self::getRREF($players, $only_students);
        $ratings = self::addMinValue($rref->getColumn($players->count()));
        self::storeRatings($ratings, $players, $only_students);
        $ranks = self::convertToRanks($rref->getColumn($players->count()));
        self::storeRanks($ranks, $players, $only_students);
    }

    public static function updateRanks ($only_students) {
        $all_players = Player::all();
        self::calculateRanks($all_players, false);

        if ($only_students === true){
            $student_players = Player::where('is_student', true)->get();
            self::calculateRanks($student_players, true);
        }
    }
}
