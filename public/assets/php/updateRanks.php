<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Game;
use RCAuth;
use App\Models\User;
use App\Models\Player;
use Illuminate\Database\Eloquent\SoftDeletes;
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;

function getRREF($players, $only_students) {
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

function storeRanks ($ranks, $players, $only_students) {
    $index = 0;
    if ($only_students === true){
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
function addMinValue ($array) {
    $min = (-1) * min($array);
    foreach ($array as $key=>$item) {
        $array[$key] += $min;
    }
    return $array;
}

function convertToRanks ($row_ranks) {
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

function storeRatings ($ratings, $players, $only_students) {
    $index = 0;
    if ($only_students){
        foreach ($players as $player) {
            $player->rating_students = intval($ratings[$index]);
            $index+=1;
            $player->update();
        }
    } else {
        foreach ($players as $player) {
            $player->rating_all = intval($ratings[$index]);
            $index+=1;
            $player->update();
        }
    }
}

function calculateRanks ($players, $only_students) {
    $rref = getRREF($players, $only_students);
    $ratings = addMinValue($rref->getColumn($players->count()));
    storeRatings($ratings, $players, $only_students);
    $ranks = convertToRanks($rref->getColumn($players->count()));
    storeRanks($ranks, $players, $only_students);
}

function updateRanks ($only_students) {
    $all_players = Player::all();
    calculateRanks($all_players, false);

    if ($only_students === true){
        $student_players = Player::where('is_student', true)->get();
        calculateRanks($student_players, true);
    }
}
