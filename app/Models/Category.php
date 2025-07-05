<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Task;
use App\Models\CategoryUserShare; // ← 追加

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'icon_path', // ← これを追加
        'public_share_token',
        'is_public_shared',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            // 初回登録時にUUIDを生成
            $category->public_share_token = Str::uuid()->toString();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
public function sharedUsers()
{
    return $this->belongsToMany(User::class, 'category_user_shares', 'category_id', 'shared_user_id')
        ->using(\App\Models\CategoryUserShare::class)
        ->withPivot(['permission', 'confirmation_token', 'is_confirmed'])
        ->withTimestamps();
}

    public function confirmedSharedUsers()
    {
        return $this->sharedUsers()->wherePivot('is_confirmed', true);
    }

    // カテゴリに紐づくタスク
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // 追加：このカテゴリが指定ユーザーにとって編集可能か判定
    public function isEditableBy($user)
    {
        // オーナーであれば常に編集可能
        if ($this->user_id === $user->id) {
            return true;
        }

        // category_user_shares テーブルに "edit" 権限の共有があるか
        return CategoryUserShare::where('category_id', $this->id)
            ->where('shared_user_id', $user->id)
            ->where('permission', 'edit')
            ->where('is_confirmed', true)
            ->exists();
    }
}
