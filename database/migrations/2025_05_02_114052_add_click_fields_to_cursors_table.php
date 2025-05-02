<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursors', function (Blueprint $table) {
            $table->unsignedInteger('totalClick')->default(0)->after('schedule');
            $table->unsignedInteger('todayClick')->default(0)->after('totalClick');
        });
    }
    
    public function down()
    {
        Schema::table('cursors', function (Blueprint $table) {
            $table->dropColumn(['totalClick', 'todayClick']);
        });
    }
};
