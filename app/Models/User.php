<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'account_id', // ← 追加
        'button_layout',
        'google_calendar_color',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
public function sharedCategories()
{
    return $this->belongsToMany(Category::class, 'category_user_shares', 'shared_user_id', 'category_id')
        ->withPivot(['permission', 'confirmation_token', 'is_confirmed'])
        ->withTimestamps()
        ->wherePivot('is_confirmed', true); // ← これを追加
}

    /**
     * アカウントIDの自動生成（TSKxxxx形式）
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->account_id)) {
                do {
                    $accountId = 'TSK' . rand(1000, 9999);
                } while (self::where('account_id', $accountId)->exists());

                $user->account_id = $accountId;
            }
        });
    }
}
