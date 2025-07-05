<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
// database/migrations/xxxx_xx_xx_add_button_layout_to_users_table.php

public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('button_layout')->default('menu_above'); // デフォルト：メニューバー上
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('button_layout');
    });
}

};
