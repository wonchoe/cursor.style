<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('cursors', function (Blueprint $table) {
            $table->string('slug_url')->nullable()->unique()->after('name_en');
        });
    }
    
    public function down(): void
    {
        Schema::table('cursors', function (Blueprint $table) {
            $table->dropColumn('slug_url');
        });
    }
};
