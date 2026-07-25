<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cashout_logs', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('cashout_logs', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable(false)->change();
        });
    }
};
