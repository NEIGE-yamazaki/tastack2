<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAIAdvice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;
    public $user;
    public $prompt;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, User $user, string $prompt)
    {
        $this->task = $task;
        $this->user = $user;
        $this->prompt = $prompt;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => 'あなたは優秀なAIアドバイザーです。'],
                        ['role' => 'user', 'content' => $this->prompt],
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $aiAdvice = $response->json('choices.0.message.content');
                
                // タスクのAIアドバイスを更新
                $this->task->update([
                    'ai_advice' => $aiAdvice,
                    'used_ai_advisor' => true,
                ]);

                // ユーザーの使用回数を更新
                $this->user->increment('ai_advisor_used_today');
                $this->user->update(['ai_advisor_last_used_at' => now()]);

                Log::info('AI アドバイス生成成功', [
                    'task_id' => $this->task->id,
                    'user_id' => $this->user->id,
                ]);
            } else {
                Log::error('AI アドバイス生成失敗', [
                    'task_id' => $this->task->id,
                    'user_id' => $this->user->id,
                    'response' => $response->body(),
                ]);
                
                // 失敗時のデフォルトメッセージ
                $this->task->update([
                    'ai_advice' => '（AIアドバイスの取得に失敗しました）',
                    'used_ai_advisor' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI アドバイス生成エラー', [
                'task_id' => $this->task->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            
            // エラー時のデフォルトメッセージ
            $this->task->update([
                'ai_advice' => '（AIアドバイスの取得に失敗しました）',
                'used_ai_advisor' => false,
            ]);
        }
    }
}
