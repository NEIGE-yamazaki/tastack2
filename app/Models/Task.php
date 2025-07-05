<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // 追記：一括代入（createなど）で保存可能なカラム
    protected $fillable = [
        'category_id',
        'title',
        'due_date',
        'note',
        'is_done',
        'done_comment',
        'used_ai_advisor',
        'ai_advice',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
