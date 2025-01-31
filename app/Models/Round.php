<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $table = "AcademicAffairsOperations.mcsp_darts.rounds";
    protected $primaryKey = "id";

    protected $fillable = ['game_id', 'round_number', 'score1', 'score2', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];

    public function game () {
        return $this->belongsTo(Game::class, 'id', 'game_id');
    }
}
