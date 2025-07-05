<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('category_user_shares', function (Blueprint $table) {
            $table->string('confirmation_token')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('category_user_shares', function (Blueprint $table) {
            $table->string('confirmation_token')->nullable(false)->change();
        });
    }
};
