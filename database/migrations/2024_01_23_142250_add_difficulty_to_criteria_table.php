<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_criteria', function (Blueprint $table) {
            $table->string('difficulty')->nullable();
        });

        Schema::table('follower_criteria', function (Blueprint $table) {
            $table->string('difficulty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_criteria', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });

        Schema::table('follower_criteria', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
};
