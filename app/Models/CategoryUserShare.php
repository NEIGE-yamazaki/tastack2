<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // ← Model → Pivot に変更

class CategoryUserShare extends Pivot
{
    protected $table = 'category_user_shares';

    protected $fillable = [
        'category_id',
        'shared_user_id',
        'permission',
        'confirmation_token',
        'is_confirmed',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
    ];

    // リレーション
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sharedUser()
    {
        return $this->belongsTo(User::class, 'shared_user_id');
    }
}
