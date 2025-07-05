<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_group_id',
        'identifier', // メールアドレス or アカウントID
        'user_id',    // 新たに追加されたリレーション先
    ];

    /**
     * 所属する共有グループ
     */
    public function group()
    {
        return $this->belongsTo(ShareGroup::class, 'share_group_id');
    }

    /**
     * 対応するユーザー（アカウントID／メールを元に保存時に紐づけ）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
