<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Game;
use MathPHP\LinearAlgebra\MatrixFactory;

class Player extends Model
{
    protected $table = "AcademicAffairsOperations.mcsp_darts.players";
    protected $primaryKey = "id";

    protected $fillable = ['rcid', 'is_student', 'fkey_term_id', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];
    protected $with = ['user', 'term'];

    public function user () {
        return $this->hasOne(User::class, 'RCID', 'rcid');
    }

    public function term () {
        return $this->hasOne(Term::class, 'id', 'fkey_term_id');
    }

    public static function processPlayer($player_rcid, $submitter_rcid, $submitted_to_term_id) {
        $player = Player::where('rcid', $player_rcid)->where('fkey_term_id', $submitted_to_term_id)->first();
        if (empty($player->id)) {
            $user = User::find($player_rcid);
            $player = new Player ([
                'rcid' => $player_rcid,
                'is_student' => ($user->Student === 'Yes'),
                'fkey_term_id' => $submitted_to_term_id,
                'created_by' => $submitter_rcid,
                'updated_by' => $submitter_rcid
            ]);
            $player->save();
        }
        return $player;
    }

    public function gamesPlayed ($students_only) {
        $player_id = $this->id;
        $all_games = Game::where('fkey_player1', $player_id)
                            ->orWhere('fkey_player2', $player_id);
        if ($students_only) {
            $all_games = $all_games->studentPlayers();
        }
        return $all_games->orderBy('created_at', 'DESC')->get();
    }

    public function numGamesPlayed ($students_only) {
        return $this->gamesPlayed($students_only)->count();
    }

    public function numUniqueOpponents ($students_only) {
        $games = $this->gamesPlayed($students_only);
        $this_id = $this->id;
        $unique_opponents = $games->map(function ($game) use ($this_id) {
            return ($game->fkey_player1 == $this_id ? $game->fkey_player2 : $game->fkey_player1);
        })->unique();
        return $unique_opponents->count();
    }

    public function numGamesStreak ($only_students) {
        $games = $this->gamesPlayed($only_students);
        // games are sorted in descending order
        $streak = 0;
        foreach ($games as $game) {
            if (((int)$game->fkey_player1 === $this->id && $game->player1_score > $game->player2_score) ||
                ((int)$game->fkey_player2 === $this->id && $game->player2_score > $game->player1_score)){
                // $this player won
                if ($streak < 0) {
                    break;
                }
                $streak += 1;
            } else {
                // $this player lost
                if ($streak > 0) {
                    break;
                }
                $streak -= 1;
            }
        }
        return $streak;
    }

    public function scopeSearch(Builder $query, $search_term) {
        if (!empty($search_term)) {
            if (is_numeric($search_term)) {
                $query->where(function ($query) use ($search_term) {
                    $query->where('net_points', $search_term)
                          ->orWhere('rank', $search_term);
                });
            } else {
                $query->whereHas("user", function ($query) use ($search_term) {
                    $query->where('rc_full_name', 'LIKE', sprintf('%%%s%%', $search_term));
                });
            }
        }
    }

    private static function getRREF($players, $only_students) {
        foreach ($players as $player) {
            $total_num_games = $player->numGamesPlayed($only_students);

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

    public function updateTotalNet ($only_students, $rcid_auth) {
        $gamesPlayed = $this->gamesPlayed($only_students);
        $pointsFor = 0;
        $pointsAgainst = 0;

        foreach ($gamesPlayed as $game) {
            if ((int)$game->fkey_player1 === $this->id) {
                $pointsFor += $game->player1_score;
                $pointsAgainst += $game->player2_score;
            } else {
                $pointsFor += $game->player2_score;
                $pointsAgainst += $game->player1_score;
            }
        }

        if ($only_students) {
            $this->total_net_students = $pointsFor - $pointsAgainst;
        } else {
            $this->total_net_all = $pointsFor - $pointsAgainst;
        }
        $this->updated_by = $rcid_auth;
        $this->update();
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
                $player->rating_students = number_format($ratings[$index], 2, '.', '');
                $index+=1;
                $player->update();
            }
        } else {
            foreach ($players as $player) {
                $player->rating_all = number_format($ratings[$index], 2, '.', '');
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

    public static function updateRanks ($only_students, Player $player1, Player $player2, $submitted_to_term_id, $rcid_auth) {
        $player1->updateTotalNet(false, $rcid_auth);
        $player2->updateTotalNet(false, $rcid_auth);
        $all_players = Player::where('fkey_term_id', $submitted_to_term_id)->get()
                                ->filter(function ($player) {
                                    return $player->numUniqueOpponents(false) >= 4;
                                });
        // not considering players that played less than 4 unique people to make sure the matrix is closed.
        if ($all_players->count() > 0) {
            self::calculateRanks($all_players, false);
        }

        if ($only_students){
            $player1->updateTotalNet(true, $rcid_auth);
            $player2->updateTotalNet(true, $rcid_auth);
            $student_players = Player::where('fkey_term_id', $submitted_to_term_id)->where('is_student', true)->get()
                                        ->filter(function ($player) {
                                            return $player->numUniqueOpponents(true) >= 4;
                                        });
            if ($student_players->count() > 0) {
                self::calculateRanks($student_players, true);
            }
        }
    }
}
