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

function getRREF($players) {
    $one_player_array = array();
    $one_player_net_points = 0;
    foreach ($players as $player) {
        $total_num_games = Game::where('fkey_player1', $player->id)
                                    ->orWhere('fkey_player2', $player->id)
                                    ->count();
        foreach ($players as $opponent) {
            if ($opponent->id !== $player->id){
                $games_opponent = Game::where(function ($query) use ($player, $opponent){
                                                    $query->where('fkey_player1', $player->id)
                                                            ->where('fkey_player2', $opponent->id);
                                                })
                                                ->orWhere(function ($query) use ($player, $opponent) {
                                                    $query->where('fkey_player2', $player->id)
                                                            ->where('fkey_player1', $opponent->id);
                                                })->get();
                foreach ($games_opponent as $one_game) {
                    if (intval($one_game->fkey_player1) === $player->id) {
                        $player_score = $one_game->player1_score;
                        $opponent_score = $one_game->player2_score;
                    } else {
                        $player_score = $one_game->player2_score;
                        $opponent_score = $one_game->player1_score;
                    }
                    $one_player_net_points += $player_score - $opponent_score;
                }
                $one_player_array[] = $games_opponent->count()*(-1);
            } else {
                $one_player_array[] = $total_num_games;
                $total_num_games=0;
            }
        }
        $one_player_array[] = $one_player_net_points;
        $player->total_net = $one_player_net_points;
        $player->update();
        $matrix[] = $one_player_array;
        $one_player_array = array();
        $one_player_net_points = 0;
    }
    $math_matrix = MatrixFactory::create($matrix);
    return ($math_matrix->rref());
}

function storeRanks ($ranks, $players, $rank_students) {
    $index = 0;
    if ($rank_students === true){
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

function updateRanks ($only_students) {
    $all_players = Player::all();
    $rref_all = getRREF($all_players);
    $ranks = convertToRanks($rref_all->getColumn($all_players->count()));
    storeRanks($ranks, $all_players, false);

    if ($only_students === true){
        $student_players = Player::where('is_student', true)->get();
        $rref_students = getRREF($student_players);
        $ranks = convertToRanks($rref_students->getColumn($student_players->count()));
        storeRanks($ranks, $student_players, true);
    }
}
