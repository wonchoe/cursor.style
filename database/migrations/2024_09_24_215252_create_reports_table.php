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
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // ID with auto-increment
            $table->date('date'); // Date field
            $table->string('project'); // Project name
            $table->integer('installs')->default(0); // Default to 0 installs
            $table->integer('uninstalls')->default(0); // Default to 0 uninstalls
            $table->integer('users_total')->default(0); // Default to 0 total users
            $table->float('rating_value')->default(0); // Default to 0 for rating value
            $table->integer('feedbacks_total')->default(0); // Default to 0 feedbacks
            $table->integer('overal_rank')->default(0); // Default to 0 overall rank
            $table->integer('cat_rank')->default(0); // Default to 0 category rank
            $table->timestamps(); // Standard created_at and updated_at fields
            $table->index(['date', 'project']); // Index on date and project columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
