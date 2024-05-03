<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Player;

class updateRanks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $only_students;
    private $player1;
    private $player2;
    private $current_term;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($only_students, Player $player1, Player $player2, $current_term)
    {
        $this->only_students = $only_students;
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->current_term = $current_term;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Player::updateRanks($this->only_students, $this->player1, $this->player2, $this->current_term);
    }
}
