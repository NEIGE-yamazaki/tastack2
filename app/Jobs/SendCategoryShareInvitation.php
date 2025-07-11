<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CategoryUserShare;
use App\Models\User;
use App\Notifications\CategoryShareInvitation;

class SendCategoryShareInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $share;
    public $inviterName;

    /**
     * Create a new job instance.
     */
    public function __construct(CategoryUserShare $share, string $inviterName)
    {
        $this->share = $share;
        $this->inviterName = $inviterName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->share->shared_user_id);
        
        if ($user) {
            $user->notify(new CategoryShareInvitation($this->share, $this->inviterName));
        }
    }
}
