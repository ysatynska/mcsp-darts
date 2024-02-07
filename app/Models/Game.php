<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = "ysatynska_training.dbo.pp_games_ys";
    protected $primaryKey = "id";

    protected $fillable = ['player1_rcid', 'player2_rcid', 'player1_score', 'player2_score', 'fkey_doubles', 'created_by', 'updated_by', 'player1_name', 'player2_name'];
    protected $dates = ['deleted_at'];
}
