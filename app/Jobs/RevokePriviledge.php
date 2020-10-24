<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\Priviledge;

use App\Repositories\PriviledgeRepository;

class RevokePriviledge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $user;
    private $priviledge;
    private $targetId;

    public function __construct(User $user, Priviledge $priviledge, $targetId = null)
    {
        $this->user = $user;
        $this->priviledge = $priviledge;
        $this->targetId = $targetId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $priviledgeRepository = new PriviledgeRepository;
        $priviledgeRepository->target($this->targetId)->user($this->user)->priviledge($this->priviledge)->destroy();
    }
}
