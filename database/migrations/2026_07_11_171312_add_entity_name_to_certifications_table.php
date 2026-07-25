<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('entity_name')->nullable()->after('plan');
        });
    }

    public function down()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('entity_name');
        });
    }
};
