<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations_cursor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cursor_id');
            $table->string('lang', 5);
            $table->string('name');
            $table->timestamps();

            $table->unique(['cursor_id', 'lang']);
            $table->foreign('cursor_id')->references('id')->on('cursors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations_cursor');
    }
};
