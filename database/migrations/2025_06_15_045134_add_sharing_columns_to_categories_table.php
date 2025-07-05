<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('public_share_token')->nullable()->unique()->after('name');
            $table->boolean('is_public_shared')->default(false)->after('public_share_token');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['public_share_token', 'is_public_shared']);
        });
    }
};
