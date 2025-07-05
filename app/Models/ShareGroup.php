<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * グループ作成者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * グループのメンバー一覧
     */
    public function members()
    {
        return $this->hasMany(ShareGroupMember::class);
    }
}
