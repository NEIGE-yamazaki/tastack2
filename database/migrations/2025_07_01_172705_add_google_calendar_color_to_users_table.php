<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

// database/migrations/xxxx_xx_xx_xxxxxx_add_google_calendar_color_to_users_table.php

public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_calendar_color')->nullable()->after('button_layout');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('google_calendar_color');
    });
}

};
