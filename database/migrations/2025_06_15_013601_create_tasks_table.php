<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // カテゴリに紐付く
            $table->string('title');        // タスクのタイトル
            $table->date('due_date')->nullable();   // 期日（任意）
            $table->text('note')->nullable();       // メモ欄
            $table->boolean('is_done')->default(false); // 完了フラグ
            $table->string('done_comment')->nullable(); // 完了時の一言
            $table->timestamps();           // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
