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
        Schema::create('cursor_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang', 5);
            $table->unsignedBigInteger('cursor_id');
            $table->string('name');
            $table->timestamps();
            $table->unique(['lang', 'cursor_id']);
            $table->string('name')->index(); // або: $table->index('name');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cursor_translations');
    }
};
