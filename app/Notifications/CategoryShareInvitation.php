<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Category;

class CategoryShareInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $category;
    protected $confirmationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(Category $category, string $confirmationToken)
    {
        $this->category = $category;
        $this->confirmationUrl = route('categories.share.confirm', ['token' => $confirmationToken]); // 修正
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('【Tastack】カテゴリが共有されました')
            ->greeting("{$notifiable->name}さん")
            ->line("カテゴリ「{$this->category->name}」があなたと共有されました。")
            ->action('共有を確認する', $this->confirmationUrl)
            ->line('上記のリンクからログインして共有カテゴリを確認してください。');
    }
}
