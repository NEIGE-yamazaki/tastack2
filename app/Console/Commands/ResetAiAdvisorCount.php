<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetAiAdvisorCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '全ユーザーのAIアドバイザー使用回数をリセットする';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::query()->update([
            'ai_advisor_used_today' => 0,
            'ai_advisor_last_used_at' => null,
        ]);

        $this->info('AIアドバイザー使用回数をリセットしました。');
    }
}
