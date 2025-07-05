<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE category_user_shares MODIFY permission ENUM('view', 'edit', 'full') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE category_user_shares MODIFY permission ENUM('view', 'edit') NOT NULL");
    }
};
