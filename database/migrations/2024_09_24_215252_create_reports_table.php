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
            $table->id(); // ID з автоінкрементом
            $table->date('date'); // Поле дати
            $table->integer('installs'); // Інсталяції
            $table->integer('uninstalls'); // Деінсталяції
            $table->integer('users_total'); // Загальна кількість користувачів
            $table->float('rating_value'); // Значення рейтингу
            $table->integer('feedbacks_total'); // Загальна кількість відгуків
            $table->integer('overal_rank'); // Загальний рейтинг
            $table->integer('cat_rank'); // Рейтинг у категорії
            $table->timestamps(); // Стандартні поля created_at та updated_at
        });
    }

    public function down()
    {
       // Schema::dropIfExists('reports');
    }
};
