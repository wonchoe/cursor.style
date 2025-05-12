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
        Schema::create('collections_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang', 5);
            $table->unsignedBigInteger('collection_id');
            $table->string('name');
            $table->text('short_desc');
            $table->text('desc');
            $table->timestamps();
    
            $table->unique(['lang', 'collection_id']);
            $table->index('name');
            $table->foreign('collection_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
    
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections_translations');
    }
};
