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
        Schema::create('seo_cursor_texts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cursor_id')->index();
            $table->string('lang', 5)->index();

            $table->string('seo_title', 100)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_page')->nullable();

            $table->string('batch_id', 100)->nullable()->index();
            $table->string('status', 20)->default('new')->index();

            $table->text('error_message')->nullable();

            $table->timestamps();

            // Індекси для швидкого пошуку
            $table->unique(['cursor_id', 'lang']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_cursor_texts');
    }
};
