<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursorTagsTable extends Migration
{
    public function up()
    {
        Schema::create('cursor_tag_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cursor_id');
            $table->string('lang', 5);     // ISO-код мови
            $table->text('tags');          // Теги як текст, через кому або пробіл

            $table->timestamps();

            // Індекси
            $table->index('cursor_id');
            $table->index('lang');
            $table->unique(['cursor_id', 'lang']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cursor_tag_translations');
    }
}
