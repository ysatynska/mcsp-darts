<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class updateRanks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $only_students;
    /**
     * Create a new job instance.
     *
     * @return void 
     */
    public function __construct($only_students)
    {
        $this->only_students = $only_students;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App\Models\Player::updateRanks($this->only_students);
    }
}
