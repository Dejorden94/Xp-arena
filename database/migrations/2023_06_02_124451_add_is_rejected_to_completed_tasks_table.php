<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRejectedToCompletedTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_tasks', function (Blueprint $table) {
            $table->boolean('is_rejected')->default(0); // Voeg de nieuwe kolom toe
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed_tasks', function (Blueprint $table) {
            $table->dropColumn('is_rejected'); // Verwijder de kolom als we de migratie terugdraaien
        });
    }
}
