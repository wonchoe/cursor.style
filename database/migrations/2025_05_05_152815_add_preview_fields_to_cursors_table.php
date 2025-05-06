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
            $table->string('c_file_prev')->nullable();
            $table->string('p_file_prev')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('cursors', function (Blueprint $table) {
            $table->dropColumn(['c_file_prev', 'p_file_prev']);
        });
    }
};
