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
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('extension_install')->default(0)->after('uninstalls');
            $table->integer('extension_active')->default(0)->after('extension_install');
            $table->integer('extension_update')->default(0)->after('extension_active');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['extension_install', 'extension_active', 'extension_update']);
        });
    }
};
