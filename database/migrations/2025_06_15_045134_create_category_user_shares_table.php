<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_user_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('permission', ['view', 'edit'])->default('view');
            $table->string('confirmation_token')->unique(); // 確認メール用トークン
            $table->boolean('is_confirmed')->default(false); // メール承認済みフラグ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_user_shares');
    }
};
