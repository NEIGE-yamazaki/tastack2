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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('ai_advisor_used_today')->default(0)->after('remember_token');
            $table->timestamp('ai_advisor_last_used_at')->nullable()->after('ai_advisor_used_today');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ai_advisor_used_today', 'ai_advisor_last_used_at']);
        });
    }
};
