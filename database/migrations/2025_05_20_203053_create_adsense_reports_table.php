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
        Schema::create('adsense_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('estimated_earnings', 8, 2)->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('impressions')->nullable();
            $table->integer('page_views')->nullable();
            $table->decimal('impressions_rpm', 6, 2)->nullable();
            $table->decimal('cost_per_click', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adsense_reports');
    }
};
