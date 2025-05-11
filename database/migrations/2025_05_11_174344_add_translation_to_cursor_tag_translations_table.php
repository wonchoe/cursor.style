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
    public function up(): void
    {
        Schema::table('cursor_tag_translations', function (Blueprint $table) {
            $table->string('translation')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('cursor_tag_translations', function (Blueprint $table) {
            $table->dropColumn('translation');
        });
    }
};
