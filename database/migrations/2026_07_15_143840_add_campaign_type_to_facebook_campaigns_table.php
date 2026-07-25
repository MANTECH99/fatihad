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
        Schema::table('facebook_campaigns', function (Blueprint $table) {
            $table->string('campaign_type')->default('boost')->after('name');
        });
    }

    public function down()
    {
        Schema::table('facebook_campaigns', function (Blueprint $table) {
            $table->dropColumn('campaign_type');
        });
    }
};
