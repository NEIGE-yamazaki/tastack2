<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

// database/migrations/xxxx_xx_xx_change_due_date_column_type_in_tasks_table.php
public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dateTime('due_date')->change(); // datetime に変更
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->date('due_date')->change(); // 元に戻す場合
    });
}

};
