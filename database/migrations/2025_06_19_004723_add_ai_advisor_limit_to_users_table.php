<?php

// database/migrations/xxxx_xx_xx_add_ai_advisor_limit_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('ai_advisor_limit_per_day')->default(1)->after('ai_advisor_used_today');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ai_advisor_limit_per_day');
        });
    }
};
